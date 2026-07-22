<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

use JOOservices\Flickr\Cache\NullCache;
use JOOservices\Flickr\Contracts\Cache\FlickrCacheContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use Throwable;

/**
 * Fetches flickr.people.getLimits once and caches the photo maxupload bytes.
 */
final class CachedUploadLimitResolver
{
    private const string CACHE_KEY = 'flickr:upload-limit:photos-maxupload';

    private const int DEFAULT_TTL_SECONDS = 86400;

    private ?int $memoized = null;

    private bool $resolved = false;

    public function __construct(
        private RawApiServiceContract $raw,
        private FlickrCacheContract $cache = new NullCache,
        private int $ttlSeconds = self::DEFAULT_TTL_SECONDS,
    ) {}

    public function maxUploadBytes(): ?int
    {
        if ($this->resolved) {
            return $this->memoized;
        }

        $cached = $this->readCached();
        if ($cached !== null) {
            return $this->remember($cached);
        }

        $bytes = $this->fetchFromApi();
        if ($bytes !== null) {
            $this->cache->put(self::CACHE_KEY, $bytes, $this->ttlSeconds);
        }

        return $this->remember($bytes);
    }

    private function readCached(): ?int
    {
        $cached = $this->cache->get(self::CACHE_KEY);
        if (is_int($cached) && $cached > 0) {
            return $cached;
        }

        if (is_string($cached) && ctype_digit($cached) && (int) $cached > 0) {
            return (int) $cached;
        }

        return null;
    }

    private function fetchFromApi(): ?int
    {
        try {
            $response = $this->raw->call('flickr.people.getLimits');
        } catch (Throwable) {
            return null;
        }

        if (! $response->ok) {
            return null;
        }

        return $this->extractMaxUpload($response->data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function extractMaxUpload(array $data): ?int
    {
        $person = $data['person'] ?? [];
        $photos = is_array($person) ? ($person['photos'] ?? []) : [];
        $maxUpload = is_array($photos) ? ($photos['maxupload'] ?? null) : null;

        if ($maxUpload === null || $maxUpload === '' || ! is_numeric($maxUpload)) {
            return null;
        }

        $bytes = (int) $maxUpload;

        return $bytes > 0 ? $bytes : null;
    }

    private function remember(?int $bytes): ?int
    {
        $this->resolved = true;
        $this->memoized = $bytes;

        return $bytes;
    }
}

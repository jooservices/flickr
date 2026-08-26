<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Metadata;

use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use RuntimeException;

final class FlickrMethodRegistry
{
    private const REGISTRY_PATH = __DIR__ . '/../../resources/method-registry.php';

    /** @var array<string, FlickrMethodDefinition> */
    private array $methods;

    /**
     * @param array<string, mixed>|null $records override for tests only
     */
    public function __construct(?array $records = null)
    {
        if ($records === null) {
            $path = self::REGISTRY_PATH;
            if (is_file($path) === false) {
                throw new RuntimeException('The frozen method registry resource is missing.');
            }

            /** @var array<string, mixed> $loaded */
            $loaded = require $path;
            $records = $loaded['methods'] ?? [];
        }

        $methods = [];

        foreach ((array) $records as $name => $record) {
            $methodRecord = [];

            foreach ((array) $record as $fieldKey => $fieldValue) {
                $methodRecord[(string) $fieldKey] = $fieldValue;
            }

            $methods[(string) $name] = self::hydrateDefinition((string) $name, $methodRecord);
        }

        $this->methods = $methods;
    }

    public function find(string $method): ?FlickrMethodDefinition
    {
        return $this->methods[$method] ?? null;
    }

    /** @return array<string, FlickrMethodDefinition> */
    public function all(): array
    {
        return $this->methods;
    }

    /** @return list<string> */
    public function names(): array
    {
        return array_keys($this->methods);
    }

    public function suggest(string $method): ?string
    {
        $best = null;
        $bestDistance = PHP_INT_MAX;

        foreach ($this->names() as $candidate) {
            $distance = levenshtein($method, $candidate);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $candidate;
            }
        }

        if ($best === null || $bestDistance > self::maxUsefulDistance($method, $best)) {
            return null;
        }

        return $best;
    }

    /**
     * Nearest-neighbor Levenshtein always finds *some* closest name; without
     * a relative cutoff, an unrelated/garbage input gets phrased as a
     * misleading "Did you mean ...?" suggestion. A third of the longer
     * name's length (minimum 3) comfortably covers real typos while
     * rejecting genuinely unrelated input.
     */
    private static function maxUsefulDistance(string $method, string $candidate): int
    {
        return max(3, (int) floor(max(strlen($method), strlen($candidate)) / 3));
    }

    /**
     * @param array<string, mixed> $record
     */
    private static function hydrateDefinition(string $name, array $record): FlickrMethodDefinition
    {
        $verb = self::field($record, 'verb', 'GET');
        $permission = self::field($record, 'permission', 'none');

        return new FlickrMethodDefinition(
            name: $name,
            httpMethod: HttpMethod::from($verb),
            permission: AuthPermission::from($permission),
            cacheable: ($record['cacheable'] ?? false) === true,
            available: ($record['available'] ?? true) === true,
            deprecationReason: isset($record['deprecation_reason']) && is_string($record['deprecation_reason'])
                ? $record['deprecation_reason']
                : null,
            docsUrl: isset($record['docs_url']) && is_string($record['docs_url']) ? $record['docs_url'] : null,
        );
    }

    /**
     * @param array<string, mixed> $record
     * @param string $key
     */
    private static function field(array $record, string $key, string $fallback): string
    {
        $value = $record[$key] ?? $fallback;

        return is_string($value) ? $value : $fallback;
    }
}

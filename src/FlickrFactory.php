<?php

declare(strict_types=1);

namespace JOOservices\Flickr;

use JOOservices\Client\Client\HttpClient;
use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Flickr\Api\Api;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Auth\OAuth1Authenticator;
use JOOservices\Flickr\Auth\OAuth1Signer;
use JOOservices\Flickr\Auth\OAuthRequestParamSigner;
use JOOservices\Flickr\Cache\NullCache;
use JOOservices\Flickr\Client\ApiClient;
use JOOservices\Flickr\Client\ClientV4Transport;
use JOOservices\Flickr\Client\FlickrRequestBuilder;
use JOOservices\Flickr\Client\FlickrResponseParser;
use JOOservices\Flickr\Client\MultipartRequestBuilder;
use JOOservices\Flickr\Client\Psr17Factories;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Clock;
use JOOservices\Flickr\Contracts\FlickrCache;
use JOOservices\Flickr\Contracts\Sleeper;
use JOOservices\Flickr\Contracts\TokenStore;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Support\FileValidator;
use JOOservices\Flickr\Support\NativeSleeper;
use JOOservices\Flickr\Support\RandomNonceGenerator;
use JOOservices\Flickr\Support\SignatureBaseStringBuilder;
use JOOservices\Flickr\Support\SensitiveDataRedactor;
use JOOservices\Flickr\Support\SystemClock;
use JOOservices\Flickr\Upload\TicketPoller;
use JOOservices\Flickr\Upload\UploadService;

/**
 * Explicit composition root. HTTP configuration stays authoritative inside
 * client v4; this package never chooses transports or endpoints silently.
 */
final class FlickrFactory
{
    /** Documented default ceiling applied before bytes stream into multipart bodies. */
    public const DEFAULT_MAX_UPLOAD_BYTES = 104_857_600;

    public static function make(
        FlickrConfig $config,
        HttpClient $http,
        ?TokenStore $tokens = null,
        ?FlickrCache $cache = null,
        ?Psr17Factories $psr17 = null,
    ): Flickr {
        $redactor = new SensitiveDataRedactor();
        $redactor->registerSecret($config->apiKey);
        $redactor->registerSecret($config->apiSecret);

        $psr17 ??= Psr17Factories::nyholm();
        $transport = new ClientV4Transport($http, $redactor);
        $requests = new FlickrRequestBuilder($psr17, $config->userAgent);
        $multipart = new MultipartRequestBuilder($psr17, $config->userAgent);
        $parser = new FlickrResponseParser($redactor);
        $clock = new SystemClock();
        $paramSigner = new OAuthRequestParamSigner(
            new OAuth1Signer(),
            new RandomNonceGenerator(),
            $clock,
            new SignatureBaseStringBuilder(),
            $redactor,
        );
        $tokens ??= new InMemoryTokenStore();
        $cache ??= new NullCache();

        $client = new ApiClient(
            $requests,
            $transport,
            $parser,
            $paramSigner,
            $tokens,
            $cache,
            $config->apiKey,
            $config->apiSecret,
            $config->cacheTtl,
        );

        $api = new Api($client, new FlickrMethodRegistry());
        $oauth = new OAuth1Authenticator($transport, $requests, $paramSigner, $tokens, $clock, $config);
        $uploads = new UploadService(
            $api,
            $transport,
            $multipart,
            $paramSigner,
            $tokens,
            new FileValidator(self::DEFAULT_MAX_UPLOAD_BYTES),
            $parser,
            $redactor,
            $config->apiKey,
            $config->apiSecret,
        );

        return new Flickr($api, $oauth, $uploads);
    }

    /**
     * Convenience composition over client v4 defaults (curl transport,
     * client-configured TLS/timeouts). Only use it when its documented
     * defaults match your needs; otherwise inject your own HttpClient.
     */
    public static function makeDefault(
        FlickrConfig $config,
        ?TokenStore $tokens = null,
        ?FlickrCache $cache = null,
        ?Psr17Factories $psr17 = null,
    ): Flickr {
        return self::make($config, ClientBuilder::create()->build(), $tokens, $cache, $psr17);
    }

    /**
     * Bounded poller bound to the given instance's upload service.
     */
    public static function pollerFor(Flickr $flickr, ?Clock $clock = null, ?Sleeper $sleeper = null): TicketPoller
    {
        return new TicketPoller($flickr->uploads(), $clock ?? new SystemClock(), $sleeper ?? new NativeSleeper());
    }
}

<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Auth\FlickrSignerContract;
use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\Contracts\Client\FlickrUploadClientContract;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Support\FileValidator;
use JOOservices\Flickr\Support\ParameterNormalizer;
use JOOservices\Flickr\Upload\CachedUploadLimitResolver;

final class FlickrUploadClient implements FlickrUploadClientContract
{
    public function __construct(
        private FlickrConfig $config,
        private FlickrTransportContract $transport,
        private FlickrSignerContract $signer,
        private FlickrTokenStoreContract $tokens,
        private FlickrResponseParser $parser = new FlickrResponseParser,
        private FileValidator $fileValidator = new FileValidator,
        private MultipartRequestBuilder $multipart = new MultipartRequestBuilder,
        private ParameterNormalizer $normalizer = new ParameterNormalizer,
        private ?CachedUploadLimitResolver $uploadLimitResolver = null,
    ) {}

    public function upload(UploadPhotoData $data): UploadResultData
    {
        $parameters = array_merge([
            'title' => $data->title,
            'description' => $data->description,
            'tags' => $data->tags === [] ? null : $this->formatTags($data->tags),
            'safety_level' => $data->safetyLevel,
            'content_type' => $data->contentType,
            'hidden' => $data->hidden,
            'async' => $data->async,
        ], $data->privacy->uploadFields());

        return $this->send($this->config->uploadEndpoint, $data->path, $parameters);
    }

    public function replace(ReplacePhotoData $data): UploadResultData
    {
        return $this->send($this->config->replaceEndpoint, $data->path, [
            'photo_id' => $data->photoId,
            'async' => $data->async,
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function send(string $endpoint, string $path, array $parameters): UploadResultData
    {
        $this->fileValidator->validateReadableFile($path);

        $token = $this->tokens->get();

        if ($token === null) {
            throw new AuthenticationException('Flickr upload and replace require an OAuth access token with write permission.');
        }

        $maxBytes = $this->uploadLimitResolver?->maxUploadBytes();
        if ($maxBytes !== null) {
            $this->fileValidator->validateReadableFile($path, $maxBytes);
        }

        $parameters = $this->normalizer->normalize($parameters);
        $parameters['api_key'] = $this->config->apiKey;
        $parameters = array_merge($parameters, $this->signer->sign(
            'POST',
            $endpoint,
            $parameters,
            $token->oauthToken,
            $token->oauthTokenSecret,
        ));

        $multipart = $this->multipart->build($path, $parameters);

        try {
            $raw = $this->transport->request('POST', $endpoint, [
                'multipart' => $multipart,
            ]);
        } finally {
            $this->multipart->close($multipart);
        }

        return $this->parser->parseUpload($raw);
    }

    /**
     * @param  list<string>  $tags
     */
    private function formatTags(array $tags): string
    {
        $formatted = [];

        foreach ($tags as $tag) {
            $formatted[] = str_contains($tag, ' ') ? '"'.$tag.'"' : $tag;
        }

        return implode(' ', $formatted);
    }
}

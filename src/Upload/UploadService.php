<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

use JOOservices\Client\Dto\RequestOptions;
use JOOservices\Flickr\Api\Api;
use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Auth\OAuthRequestParamSigner;
use JOOservices\Flickr\Client\FlickrResponseParser;
use JOOservices\Flickr\Config\FlickrEndpoints;
use JOOservices\Flickr\Contracts\FlickrTransport;
use JOOservices\Flickr\Contracts\TokenStore;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Enums\AuthenticationMode;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\AuthorizationException;
use JOOservices\Flickr\Exceptions\FlickrException;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Support\FileValidator;
use JOOservices\Flickr\Support\SensitiveDataRedactor;
use InvalidArgumentException;
use Throwable;

final class UploadService
{
    /**
     * Ten single-purpose collaborators (transport, signing, tokens, file and
     * response handling, secret redaction, plus the two API credentials) are
     * wired here rather than behind another abstraction, matching this
     * package's flat composition-root style (see also `ApiClient`).
     *
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function __construct(
        private readonly Api $api,
        private readonly FlickrTransport $transport,
        private readonly \JOOservices\Flickr\Client\MultipartRequestBuilder $multipart,
        private readonly OAuthRequestParamSigner $signer,
        private readonly TokenStore $tokens,
        private readonly FileValidator $files,
        private readonly FlickrResponseParser $parser,
        private readonly SensitiveDataRedactor $redactor,
        private readonly string $apiKey,
        private readonly string $apiSecret,
    ) {
    }

    public function upload(string $path, UploadOptions $options): UploadResultData
    {
        return $this->sendMultipart(FlickrEndpoints::UPLOAD, $path, $this->uploadFields($options), false);
    }

    public function replace(string $path, string $photoId): UploadResultData
    {
        if (trim($photoId) === '') {
            throw new InvalidArgumentException('A replace call requires a photo id.');
        }

        return $this->sendMultipart(FlickrEndpoints::REPLACE, $path, ['photo_id' => $photoId], true);
    }

    /**
     * Advisory quota/status lookup. Never blocks a valid upload and is never
     * cached.
     */
    public function uploadStatus(): ApiResponseData
    {
        return $this->api->call(
            'flickr.people.getUploadStatus',
            [],
            new ApiCallOptions(mode: AuthenticationMode::Authenticated),
        );
    }

    /**
     * @param list<string> $ticketIds
     */
    public function checkTickets(array $ticketIds): ApiResponseData
    {
        if ($ticketIds === []) {
            throw new InvalidArgumentException('At least one ticket id is required.');
        }

        return $this->api->call('flickr.photos.upload.checkTickets', ['tickets' => implode(',', $ticketIds)]);
    }

    /**
     * @param array<string, string> $fields
     */
    private function sendMultipart(string $endpoint, string $path, array $fields, bool $fromReplace): UploadResultData
    {
        $token = $this->tokens->get();

        if ($token === null) {
            throw new AuthenticationException('Uploads require an authenticated access token.');
        }

        if ($token->permission->satisfies(AuthPermission::Write) === false) {
            throw new AuthorizationException('Uploads require a token with write permission.');
        }

        $handle = $this->files->open($path);

        try {
            $signed = $this->signer->signedParameters(
                HttpMethod::Post,
                $endpoint,
                $fields,
                $this->apiKey,
                $this->apiSecret,
                $token->token,
                $token->tokenSecret,
            );

            $response = $this->transport->send(
                $this->multipart->build($endpoint, $signed, 'photo', basename($path), $handle),
                new RequestOptions(allowRedirects: false),
            );
        } catch (Throwable $error) {
            if ($error instanceof FlickrException) {
                throw $error;
            }

            throw new UploadException(
                'The upload request failed before Flickr returned a result.',
                previous: new UploadException(sprintf('%s: %s', $error::class, $this->redactor->redactText($error->getMessage()))),
            );
        } finally {
            fclose($handle);
        }

        $outcome = $this->parser->parseUploadXml($response);

        return new UploadResultData($outcome['photoId'], $outcome['ticketIds'], $fromReplace);
    }

    /**
     * @return array<string, string>
     */
    private function uploadFields(UploadOptions $options): array
    {
        $fields = [];

        if ($options->title !== null) {
            $fields['title'] = $options->title;
        }

        if ($options->description !== null) {
            $fields['description'] = $options->description;
        }

        if ($options->tags !== []) {
            $fields['tags'] = implode(' ', array_map(
                static function (string $tag): string {
                    $tag = str_replace('"', '', $tag);

                    return str_contains($tag, ' ') ? '"' . $tag . '"' : $tag;
                },
                $options->tags,
            ));
        }

        foreach (['is_public' => $options->isPublic, 'is_friend' => $options->isFriend, 'is_family' => $options->isFamily] as $name => $value) {
            if ($value !== null) {
                $fields[$name] = $value ? '1' : '0';
            }
        }

        if ($options->async) {
            $fields['async'] = '1';
        }

        return $fields;
    }
}

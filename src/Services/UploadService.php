<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use InvalidArgumentException;
use JOOservices\Flickr\Contracts\Client\FlickrUploadClientContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\Contracts\Services\UploadServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;

final class UploadService implements UploadServiceContract
{
    public function __construct(
        private FlickrUploadClientContract $client,
        private RawApiServiceContract $raw,
    ) {}

    public function upload(UploadPhotoData $data): UploadResultData
    {
        return $this->client->upload($data);
    }

    public function replace(ReplacePhotoData $data): UploadResultData
    {
        return $this->client->replace($data);
    }

    public function checkTickets(array $ticketIds): ApiResponseData
    {
        $ticketIds = array_values(array_filter(array_map('trim', $ticketIds), static fn (string $id): bool => $id !== ''));

        if ($ticketIds === []) {
            throw new InvalidArgumentException('At least one upload ticket id is required.');
        }

        return $this->raw->call('flickr.photos.upload.checkTickets', ['tickets' => $ticketIds]);
    }
}

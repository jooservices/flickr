<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Client\FlickrUploadClientContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\Contracts\Services\UploadServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;
use JOOservices\Flickr\Support\ListNormalizer;

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
        $ticketIds = ListNormalizer::requireNonEmptyTrimmedList($ticketIds, 'upload ticket id');

        return $this->raw->call('flickr.photos.upload.checkTickets', ['tickets' => $ticketIds]);
    }
}

<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;

interface UploadServiceContract
{
    public function upload(UploadPhotoData $data): UploadResultData;

    public function replace(ReplacePhotoData $data): UploadResultData;

    /**
     * @param  list<string>  $ticketIds
     */
    public function checkTickets(array $ticketIds): ApiResponseData;
}

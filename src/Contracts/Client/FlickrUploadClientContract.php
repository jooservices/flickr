<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Client;

use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;

interface FlickrUploadClientContract
{
    public function upload(UploadPhotoData $data): UploadResultData;

    public function replace(ReplacePhotoData $data): UploadResultData;
}

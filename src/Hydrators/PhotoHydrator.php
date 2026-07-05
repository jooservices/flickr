<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Photos\PhotoData;
use JOOservices\Flickr\DTO\Photos\PhotoExifData;
use JOOservices\Flickr\DTO\Photos\PhotoInfoData;
use JOOservices\Flickr\DTO\Photos\PhotoSizeData;

final class PhotoHydrator
{
    /**
     * @return list<PhotoData>
     */
    public function photos(ApiResponseData $response): array
    {
        return array_map(
            fn (array $photo): PhotoData => $this->photo($photo),
            ResponseItemExtractor::listItems($response, ['photos.photo', 'photo']),
        );
    }

    public function photoInfo(ApiResponseData $response): PhotoInfoData
    {
        $photo = ResponseItemExtractor::singleItem($response, ['photo']);

        if ($photo === null) {
            return new PhotoInfoData('');
        }

        $id = (string) ($photo['id'] ?? '');
        unset($photo['id']);

        return new PhotoInfoData($id, $photo);
    }

    /**
     * @return list<PhotoSizeData>
     */
    public function sizes(ApiResponseData $response): array
    {
        $sizes = ResponseItemExtractor::listItems($response, ['sizes.size']);

        return array_map(
            static fn (array $size): PhotoSizeData => new PhotoSizeData(
                label: (string) ($size['label'] ?? ''),
                source: (string) ($size['source'] ?? ''),
                width: (int) ($size['width'] ?? 0),
                height: (int) ($size['height'] ?? 0),
            ),
            $sizes,
        );
    }

    public function exif(ApiResponseData $response): PhotoExifData
    {
        $exif = ResponseItemExtractor::singleItem($response, ['photo.exif', 'exif']);

        return new PhotoExifData($exif ?? []);
    }

    /**
     * @param  array<string, mixed>  $photo
     */
    private function photo(array $photo): PhotoData
    {
        return new PhotoData(
            id: (string) ($photo['id'] ?? ''),
            title: isset($photo['title']) ? (string) $photo['title'] : null,
            owner: isset($photo['owner']) ? (string) $photo['owner'] : null,
        );
    }
}

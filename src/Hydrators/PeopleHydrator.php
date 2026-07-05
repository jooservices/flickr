<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\People\PersonData;
use JOOservices\Flickr\DTO\Photos\PhotoData;

final class PeopleHydrator
{
    public function __construct(private PhotoHydrator $photos = new PhotoHydrator) {}

    public function person(ApiResponseData $response): PersonData
    {
        $person = ResponseItemExtractor::singleItem($response, ['person']);

        if ($person === null) {
            return new PersonData('');
        }

        $id = (string) ($person['nsid'] ?? $person['id'] ?? '');
        unset($person['nsid'], $person['id']);

        return new PersonData($id, $person);
    }

    /**
     * @return list<PhotoData>
     */
    public function photos(ApiResponseData $response): array
    {
        return $this->photos->photos($response);
    }
}

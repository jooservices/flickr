<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Tags\TagData;

final class TagHydrator
{
    /**
     * @return list<TagData>
     */
    public function hotList(ApiResponseData $response): array
    {
        $tags = ResponseItemExtractor::listItems($response, ['hottags.tag', 'tags.tag', 'tag']);

        return array_map(
            static function (array $tag): TagData {
                $value = (string) ($tag['_content'] ?? $tag['tag'] ?? $tag['name'] ?? '');

                return new TagData($value);
            },
            $tags,
        );
    }
}

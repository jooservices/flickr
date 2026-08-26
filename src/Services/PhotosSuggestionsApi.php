<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PhotosSuggestionsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function approveSuggestion(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.suggestions.approveSuggestion', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.suggestions.getList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function rejectSuggestion(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.suggestions.rejectSuggestion', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function removeSuggestion(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.suggestions.removeSuggestion', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function suggestLocation(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.suggestions.suggestLocation', $parameters, $options);
    }
}

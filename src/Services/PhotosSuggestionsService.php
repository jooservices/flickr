<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosSuggestionsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosSuggestionsService extends AbstractRawService implements PhotosSuggestionsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function approveSuggestion(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.suggestions.approveSuggestion', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.suggestions.getList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function rejectSuggestion(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.suggestions.rejectSuggestion', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function removeSuggestion(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.suggestions.removeSuggestion', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function suggestLocation(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.suggestions.suggestLocation', $parameters);
    }
}

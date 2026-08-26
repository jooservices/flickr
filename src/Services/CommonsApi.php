<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class CommonsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getInstitutions(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.commons.getInstitutions', $parameters, $options);
    }
}

<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\CommonsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class CommonsService extends AbstractRawService implements CommonsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInstitutions(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.commons.getInstitutions', $parameters);
    }
}

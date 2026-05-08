<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\InterestingnessServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class InterestingnessService extends AbstractRawService implements InterestingnessServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.interestingness.getList', $parameters);
    }
}

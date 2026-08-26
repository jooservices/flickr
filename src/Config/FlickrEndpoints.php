<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Config;

/**
 * Package-owned immutable endpoint URIs. The exact URI used for OAuth
 * signing is the exact URI the request is sent to; callers cannot override
 * hosts or paths.
 */
final class FlickrEndpoints
{
    public const REST = 'https://www.flickr.com/services/rest/';

    public const OAUTH_REQUEST_TOKEN = 'https://www.flickr.com/services/oauth/request_token';

    public const OAUTH_ACCESS_TOKEN = 'https://www.flickr.com/services/oauth/access_token';

    public const OAUTH_AUTHORIZE = 'https://www.flickr.com/services/oauth/authorize';

    public const UPLOAD = 'https://up.flickr.com/services/upload/';

    public const REPLACE = 'https://up.flickr.com/services/replace/';

    private function __construct()
    {
    }
}

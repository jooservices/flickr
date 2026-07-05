<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Client\JooClientTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Tests\TestCase;

final class JooClientTransportTest extends TestCase
{
    public function test_from_config_builds_transport_with_retry_settings(): void
    {
        $transport = JooClientTransport::fromConfig(new FlickrConfig('key', 'secret', retryTimes: 2));

        $this->assertInstanceOf(JooClientTransport::class, $transport);
    }
}

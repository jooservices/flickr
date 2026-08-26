<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Dto\RequestOptions;
use JOOservices\Client\Exceptions\InvalidConfigurationException;
use JOOservices\Flickr\Client\ClientV4Transport;
use JOOservices\Flickr\Client\Psr17Factories;
use JOOservices\Flickr\Exceptions\TransportException;
use JOOservices\Flickr\Tests\Support\PipelineFactory;
use LogicException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class ClientV4TransportTest extends TestCase
{
    public function testDoesNotRetryWithoutTheRequiredRedirectPolicy(): void
    {
        $http = ClientBuilder::create()
            ->withPsr18(new class implements ClientInterface {
                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    unset($request);

                    throw new LogicException('The transport must reject unsupported request options first.');
                }
            })
            ->build();
        $request = Psr17Factories::nyholm()->request->createRequest('GET', 'https://www.flickr.com/services/rest/');

        $this->expectException(InvalidConfigurationException::class);
        (new ClientV4Transport($http, PipelineFactory::redactor()))->send(
            $request,
            new RequestOptions(allowRedirects: false),
        );
    }

    public function testTransportFailureRedactsSecretsFromTheEntireExceptionChain(): void
    {
        $http = ClientBuilder::create()
            ->withPsr18(new class implements ClientInterface {
                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    throw new RuntimeException(
                        'Connection failed for https://www.flickr.com/services/rest/?oauth_token=test-api-secret',
                    );
                }
            })
            ->build();
        $request = Psr17Factories::nyholm()->request->createRequest('GET', 'https://www.flickr.com/services/rest/');

        try {
            (new ClientV4Transport($http, PipelineFactory::redactor()))->send(
                $request,
                new RequestOptions(),
            );
            self::fail('Expected a TransportException.');
        } catch (TransportException $error) {
            self::assertStringNotContainsString('test-api-secret', $error->getMessage());

            $previous = $error->getPrevious();
            self::assertNotNull($previous);
            self::assertStringNotContainsString('test-api-secret', $previous->getMessage());
            self::assertStringContainsString(RuntimeException::class, $previous->getMessage());
        }
    }
}

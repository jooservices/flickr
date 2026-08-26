<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Auth\AccessTokenData;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Client\MultipartRequestBuilder;
use JOOservices\Flickr\Client\Psr17Factories;
use JOOservices\Flickr\Config\FlickrEndpoints;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\AuthorizationException;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Support\FileValidator;
use JOOservices\Flickr\Tests\Support\FakeTransport;
use JOOservices\Flickr\Tests\Support\PipelineFactory;
use JOOservices\Flickr\Upload\TicketPoller;
use JOOservices\Flickr\Upload\TicketStatus;
use JOOservices\Flickr\Upload\UploadOptions;
use JOOservices\Flickr\Upload\UploadService;
use PHPUnit\Framework\TestCase;

final class UploadAndTicketsTest extends TestCase
{
    private FakeTransport $transport;

    private InMemoryTokenStore $tokens;

    protected function setUp(): void
    {
        $this->transport = new FakeTransport();
        $this->tokens = new InMemoryTokenStore();
    }

    private function service(int $maxBytes = 1024 * 1024): UploadService
    {
        $psr17 = Psr17Factories::nyholm();
        $redactor = PipelineFactory::redactor();

        return new UploadService(
            PipelineFactory::apiWithTokens($this->transport, $this->tokens),
            $this->transport,
            new MultipartRequestBuilder($psr17, 'jooservices-test'),
            $this->paramSigner($redactor),
            $this->tokens,
            new FileValidator($maxBytes),
            new \JOOservices\Flickr\Client\FlickrResponseParser($redactor),
            $redactor,
            'test-api-key',
            'test-api-secret',
        );
    }

    private function paramSigner(\JOOservices\Flickr\Support\SensitiveDataRedactor $redactor): \JOOservices\Flickr\Auth\OAuthRequestParamSigner
    {
        return new \JOOservices\Flickr\Auth\OAuthRequestParamSigner(
            new \JOOservices\Flickr\Auth\OAuth1Signer(),
            new class implements \JOOservices\Flickr\Contracts\NonceGenerator {
                public function generate(): string
                {
                    return 'fixed-nonce';
                }
            },
            new \JOOservices\Flickr\Tests\Support\MutableClock(),
            new \JOOservices\Flickr\Support\SignatureBaseStringBuilder(),
            $redactor,
        );
    }

    private function tempFile(string $content = 'jpeg-bytes'): string
    {
        $path = tempnam(sys_get_temp_dir(), 'flickr-test');
        file_put_contents((string) $path, $content);

        return (string) $path;
    }

    public function testUploadRequiresTokenBeforeAnyFileRead(): void
    {
        try {
            $this->service()->upload('/definitely/missing.jpg', new UploadOptions());
            self::fail('Expected AuthenticationException.');
        } catch (AuthenticationException) {
            self::assertSame(0, $this->transport->sentCount());
        }
    }

    public function testUploadRequiresWritePermission(): void
    {
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Read));

        $this->expectException(AuthorizationException::class);
        $this->service()->upload($this->tempFile(), new UploadOptions());
    }

    public function testHappySyncUploadBuildsSignedMultipartAndClosesHandle(): void
    {
        $this->tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Delete));
        $path = $this->tempFile();

        $this->transport->queue(new RawResponseData(200, [], '<rsp stat="ok"><photo id="987"/></rsp>'));

        $result = $this->service()->upload($path, new UploadOptions(title: 'T', tags: ['one', 'big dog'], isPublic: true));

        self::assertSame('987', $result->photoId);
        self::assertSame([], $result->ticketIds);

        [$request] = $this->transport->sentRequests();
        self::assertSame(FlickrEndpoints::UPLOAD, (string) $request[0]->getUri());
        self::assertStringContainsString('multipart/form-data; boundary=', $request[0]->getHeaderLine('Content-Type'));

        $body = $this->transport->sentBodies()[0];
        self::assertStringContainsString('name="title"', $body);
        self::assertStringContainsString('"big dog"', $body);
        self::assertStringContainsString('name="is_public"', $body);
        self::assertStringContainsString('name="oauth_signature"', $body);
        self::assertStringContainsString('filename="' . basename($path) . '"', $body);

        unlink($path);
    }

    public function testAsyncUploadReturnsTicketIds(): void
    {
        $this->tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Write));
        $this->transport->queue(new RawResponseData(200, [], '<rsp stat="ok"><ticketid id="t1"/><ticketid id="t2"/></rsp>'));

        $result = $this->service()->upload($this->tempFile(), new UploadOptions(async: true));

        self::assertSame(['t1', 't2'], $result->ticketIds);
    }

    public function testReplaceCarriesPhotoIdAndFlagsResult(): void
    {
        $this->tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Write));
        $this->transport->queue(new RawResponseData(200, [], '<rsp stat="ok"><photo id="42"/></rsp>'));

        $result = $this->service()->replace($this->tempFile(), '42');

        self::assertTrue($result->fromReplace);
        self::assertSame('42', $result->photoId);

        $body = $this->transport->sentBodies()[0];
        self::assertStringContainsString('name="photo_id"', $body);
    }

    /**
     * @return iterable<string, array{0: ?string, 1: ?string}>
     */
    public static function provideBrokenFiles(): iterable
    {
        yield 'blank path' => ['   ', null];
        yield 'missing file' => ['/definitely/not/here.bin', null];
        yield 'empty content' => [null, ''];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideBrokenFiles')]
    public function testFileValidationRejectsBadTargets(?string $path, ?string $content): void
    {
        if ($content !== null) {
            $path = $this->tempFile($content);
        }

        self::assertNotNull($path);

        $this->expectException(UploadException::class);
        (new FileValidator(1024))->open($path);
    }

    public function testOversizedFileRejectedByConfiguredLimit(): void
    {
        $path = $this->tempFile(str_repeat('x', 2048));
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Write));

        $this->expectException(UploadException::class);
        $service = $this->service(maxBytes: 1024);
        try {
            $service->upload($path, new UploadOptions());
        } finally {
            unlink($path);
        }
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function provideBrokenXml(): iterable
    {
        yield 'doctype' => ['<!DOCTYPE rsp SYSTEM "x"><rsp stat="ok"></rsp>'];
        yield 'malformed' => ['<rsp stat='];
        yield 'wrong root' => ['<foo stat="ok"/>'];
        yield 'missing stat' => ['<rsp><photo id="1"/></rsp>'];
        yield 'no ids' => ['<rsp stat="ok"></rsp>'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideBrokenXml')]
    public function testBrokenUploadXmlFailsClearly(string $body): void
    {
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Write));
        $this->transport->queue(new RawResponseData(200, [], $body));
        $path = $this->tempFile();

        $this->expectException(\JOOservices\Flickr\Exceptions\FlickrException::class);

        try {
            $this->service()->upload($path, new UploadOptions());
        } finally {
            unlink($path);
        }
    }

    public function testFailedUploadSurfacesSafeErrorMetadataWithoutRetry(): void
    {
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Write));
        $this->transport->queue(new RawResponseData(
            200,
            [],
            '<rsp stat="fail"><err code="118" msg="Invalid argument" duplicates="1" non_pro_desktop_upload_wait_time="300"/></rsp>',
        ));

        try {
            $this->service()->upload($this->tempFile(), new UploadOptions());
            self::fail('Expected UploadException.');
        } catch (UploadException $error) {
            self::assertStringContainsString('code=118', $error->getMessage());
            self::assertStringContainsString('non_pro_desktop_upload_wait_time=300', $error->getMessage());
        }

        self::assertSame(1, $this->transport->sentCount());
    }

    public function testTagsWithEmbeddedQuotesAreStrippedNotMangled(): void
    {
        $this->tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Write));
        $path = $this->tempFile();
        $this->transport->queue(new RawResponseData(200, [], '<rsp stat="ok"><photo id="1"/></rsp>'));

        $this->service()->upload($path, new UploadOptions(tags: ['foo " bar']));

        $body = $this->transport->sentBodies()[0];
        self::assertStringContainsString('"foo  bar"', $body);
        self::assertStringNotContainsString('foo " bar', $body);

        unlink($path);
    }

    public function testTransportFailureDuringUploadRedactsSecretsFromTheExceptionChain(): void
    {
        $this->tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Write));
        $path = $this->tempFile();
        $this->transport->queue(new \RuntimeException('connection reset while sending test-api-secret'));

        try {
            $this->service()->upload($path, new UploadOptions());
            self::fail('Expected an UploadException.');
        } catch (UploadException $error) {
            self::assertStringNotContainsString('test-api-secret', $error->getMessage());

            $previous = $error->getPrevious();
            self::assertNotNull($previous);
            self::assertStringNotContainsString('test-api-secret', $previous->getMessage());
            self::assertStringContainsString(\RuntimeException::class, $previous->getMessage());
        } finally {
            unlink($path);
        }
    }

    public function testUploadRejectsNonSuccessfulHttpResponse(): void
    {
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Write));
        $this->transport->queue(new RawResponseData(500, [], '<rsp stat="ok"><photo id="987"/></rsp>'));

        $this->expectException(\JOOservices\Flickr\Exceptions\InvalidResponseException::class);
        $this->service()->upload($this->tempFile(), new UploadOptions());
    }

    public function testCheckTicketsRequiresAtLeastOneTicket(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service()->checkTickets([]);
    }

    public function testCheckTicketsReturnsTheRawApiResponse(): void
    {
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Read));
        $this->transport->queue(new RawResponseData(
            200,
            [],
            '{"stat":"ok","tickets":{"ticket":[{"id":"a","complete":"1","photo_id":"p-a"}]}}',
        ));

        $response = $this->service()->checkTickets(['a']);

        self::assertTrue($response->ok);
        self::assertSame('a', $response->listAt('tickets', 'ticket')[0]['id']);
    }

    public function testUploadStatusReturnsAdvisoryResponseWithoutCaching(): void
    {
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Read));
        $this->transport->queue(
            new RawResponseData(200, [], '{"stat":"ok","user":{"ispro":"0"}}'),
            new RawResponseData(200, [], '{"stat":"ok","user":{"ispro":"1"}}'),
        );

        $service = $this->service();
        $first = $service->uploadStatus();
        $second = $service->uploadStatus();

        self::assertSame('0', $first->mapAt('user')['ispro']);
        self::assertSame('1', $second->mapAt('user')['ispro']);
        self::assertSame(2, $this->transport->sentCount());
    }

    public function testPollerWalksPendingToTerminalStates(): void
    {
        $clock = new \JOOservices\Flickr\Tests\Support\MutableClock();
        $sleeper = new \JOOservices\Flickr\Tests\Support\RecordingSleeper();
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Read));

        $pendingBody = '{"stat":"ok","tickets":{"ticket":[{"id":"a","complete":"0"},{"id":"b","complete":"0"}]}}';
        $finalBody = '{"stat":"ok","tickets":{"ticket":[{"id":"a","complete":"1","photo_id":"p-a"},{"id":"b","complete":"2"}]}}';

        $this->transport->queue(
            new RawResponseData(200, [], $pendingBody),
            new RawResponseData(200, [], $finalBody),
        );

        $results = (new TicketPoller($this->service(), $clock, $sleeper))->poll(['a', 'b'], intervalMilliseconds: 10, timeoutMilliseconds: 30_000);

        self::assertCount(2, $results);
        self::assertSame(TicketStatus::Completed, $results[0]->status);
        self::assertSame('p-a', $results[0]->photoId);
        self::assertSame(TicketStatus::Failed, $results[1]->status);
        self::assertSame([10], $sleeper->sleeps);
    }

    public function testPollerTimesOutUnknownTickets(): void
    {
        $clock = new \JOOservices\Flickr\Tests\Support\MutableClock();
        $sleeper = new \JOOservices\Flickr\Tests\Support\RecordingSleeper();
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Read));

        $pending = '{"stat":"ok","tickets":{"ticket":[{"id":"z","complete":"0"}]}}';
        $this->transport->queue(
            new RawResponseData(200, [], $pending),
            new RawResponseData(200, [], $pending),
        );

        $results = (new TicketPoller($this->service(), $clock, $sleeper))->poll(['z'], 10_000, 15_000);

        self::assertSame(TicketStatus::TimedOut, $results[0]->status);
    }

    public function testPollerDoesNotSleepBeyondItsTimeout(): void
    {
        $clock = new \JOOservices\Flickr\Tests\Support\MutableClock();
        $sleeper = new \JOOservices\Flickr\Tests\Support\RecordingSleeper();
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Read));
        $this->transport->queue(new RawResponseData(200, [], '{"stat":"ok","tickets":{"ticket":[{"id":"z","complete":"0"}]}}'));

        $results = (new TicketPoller($this->service(), $clock, $sleeper))->poll(['z'], 60_000, 1_000);

        self::assertSame(TicketStatus::TimedOut, $results[0]->status);
        self::assertSame([], $sleeper->sleeps);
        self::assertSame(1, $this->transport->sentCount());
    }

    public function testPollerMarksMissingTicketsInvalid(): void
    {
        $clock = new \JOOservices\Flickr\Tests\Support\MutableClock();
        $sleeper = new \JOOservices\Flickr\Tests\Support\RecordingSleeper();
        $this->tokens->put(new AccessTokenData('t', 's', AuthPermission::Read));

        $this->transport->queue(new RawResponseData(200, [], '{"stat":"ok","tickets":{}}'));

        $results = (new TicketPoller($this->service(), $clock, $sleeper))->poll(['ghost'], 10, 5_000);

        self::assertSame(TicketStatus::Invalid, $results[0]->status);
    }

    /**
     * @return iterable<string, list{array{0: int, 1: int}|array{0: int, 1: int, 2?: list<string>}}>
     */
    public static function provideInvalidPollConfigs(): iterable
    {
        yield 'zero interval' => [[0, 1000]];
        yield 'zero timeout' => [[10, 0]];
        yield 'no tickets' => [[10, 1000, []]];
    }

    /**
     * @param array{0: int, 1: int}|array{0: int, 1: int, 2: list<string>} $args
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideInvalidPollConfigs')]
    public function testPollerValidatesInputs(array $args): void
    {
        $tickets = $args[2] ?? ['a'];

        $this->expectException(\InvalidArgumentException::class);
        (new TicketPoller($this->service(), new \JOOservices\Flickr\Tests\Support\MutableClock(), new \JOOservices\Flickr\Tests\Support\RecordingSleeper()))
            ->poll($tickets, $args[0], $args[1]);
    }
}

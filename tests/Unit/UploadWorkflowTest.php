<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Contracts\Client\FlickrUploadClientContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\Contracts\Services\UploadServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;
use JOOservices\Flickr\DTO\Upload\UploadTicketData;
use JOOservices\Flickr\Exceptions\ConfigurationException;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Hydrators\UploadTicketHydrator;
use JOOservices\Flickr\Services\UploadService;
use JOOservices\Flickr\Support\FileValidator;
use JOOservices\Flickr\Tests\Fakes\ArrayCache;
use JOOservices\Flickr\Tests\Fakes\FakeRawApiService;
use JOOservices\Flickr\Tests\TestCase;
use JOOservices\Flickr\Upload\CachedUploadLimitResolver;
use JOOservices\Flickr\Upload\TicketPoller;
use JOOservices\Flickr\Upload\TicketStatus;

final class UploadWorkflowTest extends TestCase
{
    public function test_ticket_hydrator_maps_statuses(): void
    {
        $hydrator = new UploadTicketHydrator;
        $tickets = $hydrator->fromResponse(new ApiResponseData(
            ok: true,
            data: [
                'uploader' => [
                    'ticket' => [
                        ['id' => '1', 'complete' => '1', 'photoid' => '99'],
                        ['id' => '2', 'complete' => '0'],
                        ['id' => '3', 'complete' => '2'],
                        ['id' => '4', 'invalid' => '1'],
                    ],
                ],
            ],
        ));

        $this->assertCount(4, $tickets);
        $this->assertTrue($tickets[0]->isCompleted());
        $this->assertSame('99', $tickets[0]->photoId);
        $this->assertTrue($tickets[1]->isPending());
        $this->assertTrue($tickets[2]->isFailed());
        $this->assertTrue($tickets[3]->isFailed());
    }

    public function test_ticket_poller_returns_completed_without_sleeping_when_ready(): void
    {
        $uploads = new class implements UploadServiceContract
        {
            public ?RequestOptionsData $lastOptions = null;

            public function upload(UploadPhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function replace(ReplacePhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function checkTickets(array $ticketIds, ?RequestOptionsData $options = null): ApiResponseData
            {
                $this->lastOptions = $options;

                return new ApiResponseData(true, [
                    'uploader' => [
                        'ticket' => ['id' => $ticketIds[0], 'complete' => '1', 'photoid' => '42'],
                    ],
                ]);
            }

            public function checkTicketsData(array $ticketIds, ?RequestOptionsData $options = null): array
            {
                return [(new UploadTicketHydrator)->fromResponse($this->checkTickets($ticketIds))[0]];
            }

            public function ticketPoller(): TicketPoller
            {
                return new TicketPoller($this);
            }
        };

        $outcome = (new TicketPoller($uploads))->waitForCompletion('ticket-1', maxWaitSeconds: 5, pollIntervalSeconds: 1);

        $this->assertSame(TicketStatus::Completed, $outcome->status);
        $this->assertSame('42', $outcome->photoId);
        $this->assertNotNull($uploads->lastOptions);
        $this->assertGreaterThan(0, $uploads->lastOptions->timeoutSeconds);
        $this->assertLessThanOrEqual(5, $uploads->lastOptions->timeoutSeconds);
    }

    public function test_cached_upload_limit_resolver_handles_failures_and_cache_hit(): void
    {
        $raw = new FakeRawApiService(new ApiResponseData(true, [
            'person' => [
                'photos' => ['maxupload' => '1024'],
            ],
        ]));
        $cache = new ArrayCache;
        $resolver = new CachedUploadLimitResolver($raw, $cache);
        $this->assertSame(1024, $resolver->maxUploadBytes());
        $this->assertSame(1024, $resolver->maxUploadBytes());
        $this->assertSame(1, $raw->callCount);

        $cacheHit = new ArrayCache;
        $cacheHit->put('flickr:upload-limit:photos-maxupload', 2048);
        $fromCache = new CachedUploadLimitResolver(new FakeRawApiService, $cacheHit);
        $this->assertSame(2048, $fromCache->maxUploadBytes());

        $failing = new FakeRawApiService(new ApiResponseData(false, []));
        $resolver2 = new CachedUploadLimitResolver($failing, new ArrayCache);
        $this->assertNull($resolver2->maxUploadBytes());
        $this->assertNull($resolver2->maxUploadBytes());
        $this->assertSame(1, $failing->callCount);
    }

    public function test_ticket_poller_rejects_invalid_wait_and_handles_empty_ticket_payload(): void
    {
        $uploads = new class implements UploadServiceContract
        {
            public function upload(UploadPhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function replace(ReplacePhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function checkTickets(array $ticketIds, ?RequestOptionsData $options = null): ApiResponseData
            {
                return new ApiResponseData(true, ['uploader' => ['ticket' => []]]);
            }

            public function checkTicketsData(array $ticketIds, ?RequestOptionsData $options = null): array
            {
                return [];
            }

            public function ticketPoller(): TicketPoller
            {
                return new TicketPoller($this);
            }
        };

        try {
            (new TicketPoller($uploads))->waitForCompletion('x', maxWaitSeconds: 0);
            $this->fail('Expected ConfigurationException');
        } catch (ConfigurationException) {
            $this->addToAssertionCount(1);
        }

        $timedOut = (new TicketPoller($uploads))->waitForCompletion('x', maxWaitSeconds: 1, pollIntervalSeconds: 1);
        $this->assertSame(TicketStatus::TimedOut, $timedOut->status);
    }

    public function test_cached_upload_limit_string_cache_and_throwable(): void
    {
        $cache = new ArrayCache;
        $cache->put('flickr:upload-limit:photos-maxupload', '4096');
        $this->assertSame(4096, (new CachedUploadLimitResolver(new FakeRawApiService, $cache))->maxUploadBytes());

        $throwing = new class implements RawApiServiceContract
        {
            public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData
            {
                throw new UploadException('limits unavailable');
            }
        };
        $this->assertNull((new CachedUploadLimitResolver($throwing, new ArrayCache))->maxUploadBytes());

        $badShape = new FakeRawApiService(new ApiResponseData(true, [
            'person' => ['photos' => ['maxupload' => '']],
        ]));
        $this->assertNull((new CachedUploadLimitResolver($badShape, new ArrayCache))->maxUploadBytes());
    }

    public function test_ticket_hydrator_skips_empty_ids_and_uses_attributes(): void
    {
        $hydrator = new UploadTicketHydrator;
        $tickets = $hydrator->fromResponse(new ApiResponseData(true, [
            'uploader' => [
                'ticket' => [
                    ['id' => ''],
                    ['@attributes' => ['id' => 'attr-1', 'complete' => '1', 'photoid' => '77']],
                ],
            ],
        ]));

        $this->assertCount(1, $tickets);
        $this->assertSame('attr-1', $tickets[0]->id);
        $this->assertSame('77', $tickets[0]->photoId);
    }

    public function test_ticket_poller_returns_invalid_status(): void
    {
        $uploads = new class implements UploadServiceContract
        {
            public function upload(UploadPhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function replace(ReplacePhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function checkTickets(array $ticketIds, ?RequestOptionsData $options = null): ApiResponseData
            {
                return new ApiResponseData(true, [
                    'uploader' => [
                        'ticket' => ['id' => $ticketIds[0], 'invalid' => '1'],
                    ],
                ]);
            }

            public function checkTicketsData(array $ticketIds, ?RequestOptionsData $options = null): array
            {
                return (new UploadTicketHydrator)->fromResponse($this->checkTickets($ticketIds));
            }

            public function ticketPoller(): TicketPoller
            {
                return new TicketPoller($this);
            }
        };

        $outcome = (new TicketPoller($uploads))->waitForCompletion('bad', maxWaitSeconds: 5, pollIntervalSeconds: 1);
        $this->assertSame(TicketStatus::Invalid, $outcome->status);
    }

    public function test_file_validator_rejects_oversized_files(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'flickr-upload-');
        $this->assertNotFalse($path);
        file_put_contents($path, str_repeat('a', 20));

        try {
            $this->expectException(UploadException::class);
            (new FileValidator)->validateReadableFile($path, 10);
        } finally {
            unlink($path);
        }
    }

    public function test_ticket_poller_returns_failed_and_timed_out(): void
    {
        $uploads = new class implements UploadServiceContract
        {
            public int $calls = 0;

            public function upload(UploadPhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function replace(ReplacePhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function checkTickets(array $ticketIds, ?RequestOptionsData $options = null): ApiResponseData
            {
                $this->calls++;

                return new ApiResponseData(true, [
                    'uploader' => [
                        'ticket' => ['id' => $ticketIds[0], 'complete' => '2'],
                    ],
                ]);
            }

            public function checkTicketsData(array $ticketIds, ?RequestOptionsData $options = null): array
            {
                return (new UploadTicketHydrator)->fromResponse($this->checkTickets($ticketIds));
            }

            public function ticketPoller(): TicketPoller
            {
                return new TicketPoller($this);
            }
        };

        $failed = (new TicketPoller($uploads))->waitForCompletion('t-fail', maxWaitSeconds: 5, pollIntervalSeconds: 1);
        $this->assertSame(TicketStatus::Failed, $failed->status);

        $pending = new class implements UploadServiceContract
        {
            public function upload(UploadPhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function replace(ReplacePhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function checkTickets(array $ticketIds, ?RequestOptionsData $options = null): ApiResponseData
            {
                return new ApiResponseData(true, [
                    'uploader' => [
                        'ticket' => ['id' => $ticketIds[0], 'complete' => '0'],
                    ],
                ]);
            }

            public function checkTicketsData(array $ticketIds, ?RequestOptionsData $options = null): array
            {
                return (new UploadTicketHydrator)->fromResponse($this->checkTickets($ticketIds));
            }

            public function ticketPoller(): TicketPoller
            {
                return new TicketPoller($this);
            }
        };

        $timedOut = (new TicketPoller($pending))->waitForCompletion('t-wait', maxWaitSeconds: 1, pollIntervalSeconds: 1);
        $this->assertSame(TicketStatus::TimedOut, $timedOut->status);
    }

    public function test_upload_service_exposes_ticket_poller(): void
    {
        $client = new class implements FlickrUploadClientContract
        {
            public function upload(UploadPhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function replace(ReplacePhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }
        };
        $service = new UploadService($client, new FakeRawApiService);

        $this->assertInstanceOf(TicketPoller::class, $service->ticketPoller());
    }

    public function test_upload_service_check_tickets_data(): void
    {
        $client = new class implements FlickrUploadClientContract
        {
            public function upload(UploadPhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }

            public function replace(ReplacePhotoData $data): UploadResultData
            {
                throw new UploadException('not used');
            }
        };
        $raw = new FakeRawApiService(new ApiResponseData(true, [
            'uploader' => ['ticket' => ['id' => 't1', 'complete' => '0']],
        ]));
        $service = new UploadService($client, $raw);

        $tickets = $service->checkTicketsData(['t1']);

        $this->assertCount(1, $tickets);
        $this->assertInstanceOf(UploadTicketData::class, $tickets[0]);
        $this->assertTrue($tickets[0]->isPending());
    }
}

<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

use JOOservices\Flickr\Contracts\Services\UploadServiceContract;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Upload\TicketOutcomeData;
use JOOservices\Flickr\Exceptions\ConfigurationException;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Hydrators\UploadTicketHydrator;

/**
 * Opt-in bounded poller for async upload tickets.
 *
 * BLOCKING. Call only from CLI scripts, queue workers, or cron jobs —
 * never from a web request handler.
 */
final class TicketPoller
{
    private const int MIN_POLL_INTERVAL_SECONDS = 1;

    public function __construct(
        private UploadServiceContract $uploadService,
        private UploadTicketHydrator $hydrator = new UploadTicketHydrator,
    ) {}

    public function waitForCompletion(
        string $ticketId,
        int $maxWaitSeconds = 30,
        int $pollIntervalSeconds = 2,
    ): TicketOutcomeData {
        $ticketId = trim($ticketId);
        if ($ticketId === '') {
            throw new UploadException('Upload ticket id is required.');
        }

        if ($maxWaitSeconds < 1) {
            throw new ConfigurationException('maxWaitSeconds must be at least 1.');
        }

        $pollIntervalSeconds = max(self::MIN_POLL_INTERVAL_SECONDS, $pollIntervalSeconds);
        $deadline = hrtime(true) + ($maxWaitSeconds * 1_000_000_000);

        while (($remainingSeconds = $this->remainingSeconds($deadline)) > 0) {
            $response = $this->uploadService->checkTickets(
                [$ticketId],
                new RequestOptionsData(timeoutSeconds: $remainingSeconds),
            );

            if ($this->remainingSeconds($deadline) === 0) {
                return TicketOutcomeData::timedOut($ticketId);
            }

            $tickets = $this->hydrator->fromResponse($response);
            $ticket = $tickets[0] ?? null;

            if ($ticket === null) {
                $this->sleep($pollIntervalSeconds, $deadline);

                continue;
            }

            if ($ticket->invalid === true) {
                return new TicketOutcomeData($ticketId, TicketStatus::Invalid);
            }

            if ($ticket->isCompleted()) {
                return new TicketOutcomeData($ticketId, TicketStatus::Completed, $ticket->photoId);
            }

            if ($ticket->isFailed()) {
                return new TicketOutcomeData($ticketId, TicketStatus::Failed, $ticket->photoId);
            }

            $this->sleep($pollIntervalSeconds, $deadline);
        }

        return TicketOutcomeData::timedOut($ticketId);
    }

    private function sleep(int $seconds, int $deadline): void
    {
        $remainingSeconds = $this->remainingSeconds($deadline);
        if ($remainingSeconds === 0) {
            return;
        }

        sleep(min($seconds, $remainingSeconds));
    }

    /**
     * @phpstan-impure
     */
    private function remainingSeconds(int $deadline): int
    {
        $remainingNanoseconds = $deadline - hrtime(true);

        return $remainingNanoseconds <= 0 ? 0 : (int) ceil($remainingNanoseconds / 1_000_000_000);
    }
}

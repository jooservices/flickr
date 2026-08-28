<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

use InvalidArgumentException;
use JOOservices\Flickr\Contracts\Clock;
use JOOservices\Flickr\Contracts\Sleeper;

/**
 * Bounded ticket poller with injected clock/sleeper. Callers must not run it
 * inside latency-sensitive web requests; it never retries failed HTTP calls
 * (the Api pipeline throws) and never busy-loops.
 */
final class TicketPoller
{
    public function __construct(
        private readonly UploadService $uploads,
        private readonly Clock $clock,
        private readonly Sleeper $sleeper,
    ) {
    }

    /**
     * @param list<string> $ticketIds
     *
     * @return list<TicketPollResult> one terminal result per ticket id
     */
    public function poll(array $ticketIds, int $intervalMilliseconds = 1000, int $timeoutMilliseconds = 60_000): array
    {
        $this->assertInputs($ticketIds, $intervalMilliseconds, $timeoutMilliseconds);

        $deadline = $this->clock->now() + (int) ceil($timeoutMilliseconds / 1000);
        $maxAttempts = 1 + intdiv($timeoutMilliseconds, $intervalMilliseconds);

        /** @var array<string, TicketPollResult> $results */
        $results = [];
        $pending = $ticketIds;
        $attempts = 0;

        while ($pending !== [] && $attempts < $maxAttempts && $this->clock->now() < $deadline) {
            ++$attempts;
            $remote = $this->fetchRemoteTickets($pending);
            $pending = $this->collectResolved($pending, $remote, $results);

            if ($pending !== [] && $attempts < $maxAttempts) {
                $this->sleeper->sleep($intervalMilliseconds);
            }
        }

        foreach ($pending as $unfinishedId) {
            $results[$unfinishedId] = new TicketPollResult($unfinishedId, TicketStatus::TimedOut);
        }

        ksort($results, SORT_STRING);

        return array_values($results);
    }

    /**
     * @param list<string> $ticketIds
     *
     * @return array<string, array<string, mixed>>
     */
    private function fetchRemoteTickets(array $ticketIds): array
    {
        $response = $this->uploads->checkTickets($ticketIds);
        $tickets = $response->listAt('uploader', 'ticket');

        if ($tickets === []) {
            $tickets = $response->listAt('tickets', 'ticket');
        }

        $remote = [];

        foreach ($tickets as $ticket) {
            $ticketId = self::idString($ticket['id'] ?? null);

            if ($ticketId !== '') {
                $remote[$ticketId] = $ticket;
            }
        }

        return $remote;
    }

    /**
     * Resolves every still-pending ticket once; resolved terminal results are
     * stored into `$results` and the remaining pending ids are returned.
     *
     * @param list<string> $pending
     * @param array<string, array<string, mixed>> $remote
     * @param array<string, TicketPollResult> $results
     *
     * @return list<string>
     */
    private function collectResolved(array $pending, array $remote, array &$results): array
    {
        $remaining = [];

        foreach ($pending as $ticketId) {
            $resolved = $this->resolveTicket($ticketId, $remote[$ticketId] ?? null);

            if ($resolved->status === TicketStatus::Pending) {
                $remaining[] = $ticketId;

                continue;
            }

            $results[$ticketId] = $resolved;
        }

        return $remaining;
    }

    /**
     * @param array<string, mixed>|null $ticket
     */
    private function resolveTicket(string $ticketId, ?array $ticket): TicketPollResult
    {
        if ($ticket === null) {
            return new TicketPollResult($ticketId, TicketStatus::Invalid);
        }

        return match ($ticket['complete'] ?? null) {
            1, '1' => new TicketPollResult($ticketId, TicketStatus::Completed, $this->photoIdOrNull($ticket)),
            2, '2' => new TicketPollResult($ticketId, TicketStatus::Failed),
            0, '0' => new TicketPollResult($ticketId, TicketStatus::Pending),
            default => new TicketPollResult($ticketId, TicketStatus::Invalid),
        };
    }

    /**
     * @param array<string, mixed> $ticket
     */
    private function photoIdOrNull(array $ticket): ?string
    {
        $value = self::idString($ticket['photoid'] ?? $ticket['photo_id'] ?? null);

        return $value === '' ? null : $value;
    }

    private static function idString(mixed $value): string
    {
        if (is_int($value)) {
            return (string) $value;
        }

        return is_string($value) ? $value : '';
    }

    /**
     * @param list<string> $ticketIds
     */
    private function assertInputs(array $ticketIds, int $intervalMilliseconds, int $timeoutMilliseconds): void
    {
        if ($intervalMilliseconds <= 0 || $timeoutMilliseconds <= 0) {
            throw new InvalidArgumentException('Poll interval and timeout must be positive.');
        }

        if ($ticketIds === []) {
            throw new InvalidArgumentException('At least one ticket id is required.');
        }
    }
}

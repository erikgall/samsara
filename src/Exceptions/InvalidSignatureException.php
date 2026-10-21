<?php

namespace Samsara\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Exception thrown when webhook signature verification fails.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @phpstan-consistent-constructor
 */
class InvalidSignatureException extends SamsaraException
{
    /**
     * Render webhook verification failures with their intended HTTP status.
     */
    public function render(Request $request): JsonResponse
    {
        $status = match ($this->getCode()) {
            500     => 500,
            default => 401,
        };
        $message = match ($status) {
            500     => 'Webhook verification is not configured.',
            default => 'Unauthorized.',
        };

        return response()->json(['message' => $message], $status);
    }

    /**
     * Create an exception for an expired timestamp.
     *
     * @param  int  $tolerance  The maximum allowed age in seconds
     * @param  int  $age  The actual age of the request in seconds
     */
    public static function expiredTimestamp(int $tolerance, int $age): static
    {
        return new static(
            "Webhook signature verification failed: timestamp expired (age: {$age}s, tolerance: {$tolerance}s).",
            401,
            null,
            [
                'tolerance' => $tolerance,
                'age'       => $age,
            ]
        );
    }

    /**
     * Create an exception for a timestamp in the future.
     */
    public static function futureTimestamp(int $seconds): static
    {
        return new static(
            "Webhook signature verification failed: timestamp is {$seconds}s in the future.",
            401,
            null,
            ['seconds_in_future' => $seconds],
        );
    }

    /**
     * Create an exception for an invalid signature.
     *
     * @param  string  $expected  The expected signature
     * @param  string  $received  The received signature
     */
    public static function invalidSignature(string $expected, string $received): static
    {
        return new static(
            'Webhook signature verification failed: signature mismatch.',
            401,
            null,
            [
                'received' => $received,
            ]
        );
    }

    /**
     * Create an exception for a malformed timestamp header.
     */
    public static function invalidTimestamp(string $timestamp): static
    {
        return new static(
            'Webhook signature verification failed: invalid X-Samsara-Timestamp header.',
            401,
            null,
            ['timestamp' => $timestamp]
        );
    }

    /**
     * Create an exception for a missing signature header.
     */
    public static function missingSignature(): static
    {
        return new static(
            'Webhook signature verification failed: missing X-Samsara-Signature header.',
            401,
            null,
            ['header' => 'X-Samsara-Signature']
        );
    }

    /**
     * Create an exception for a missing timestamp header.
     */
    public static function missingTimestamp(): static
    {
        return new static(
            'Webhook signature verification failed: missing X-Samsara-Timestamp header.',
            401,
            null,
            ['header' => 'X-Samsara-Timestamp']
        );
    }

    /**
     * Create an exception for a missing webhook secret configuration.
     */
    public static function missingWebhookSecret(): static
    {
        return new static(
            'Webhook secret not configured. Set SAMSARA_WEBHOOK_SECRET in your .env file.',
            500
        );
    }
}

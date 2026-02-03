<?php

namespace Samsara\Exceptions;

/**
 * Exception thrown when webhook signature verification fails.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class InvalidSignatureException extends SamsaraException
{
    /**
     * Create an exception for an expired timestamp.
     *
     * @param  int  $tolerance  The maximum allowed age in seconds
     * @param  int  $age  The actual age of the request in seconds
     */
    public static function expiredTimestamp(int $tolerance, int $age): static
    {
        return static::make(
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
     * Create an exception for an invalid signature.
     *
     * @param  string  $expected  The expected signature
     * @param  string  $received  The received signature
     */
    public static function invalidSignature(string $expected, string $received): static
    {
        return static::make(
            'Webhook signature verification failed: signature mismatch.',
            401,
            null,
            [
                'expected' => $expected,
                'received' => $received,
            ]
        );
    }

    /**
     * Create an exception for a missing signature header.
     */
    public static function missingSignature(): static
    {
        return static::make(
            'Webhook signature verification failed: missing X-Samsara-Signature header.',
            401
        );
    }

    /**
     * Create an exception for a missing timestamp header.
     */
    public static function missingTimestamp(): static
    {
        return static::make(
            'Webhook signature verification failed: missing X-Samsara-Timestamp header.',
            401
        );
    }

    /**
     * Create an exception for a missing webhook secret configuration.
     */
    public static function missingWebhookSecret(): static
    {
        return static::make(
            'Webhook secret not configured. Set SAMSARA_WEBHOOK_SECRET in your .env file.',
            500
        );
    }
}

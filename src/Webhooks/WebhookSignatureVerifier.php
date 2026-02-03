<?php

namespace Samsara\Webhooks;

use Samsara\Exceptions\InvalidSignatureException;

/**
 * Verifies webhook signatures from Samsara.
 *
 * Samsara signs webhooks using HMAC-SHA256 with a Base64-encoded secret key.
 * The signature format is: v1=<hex_digest>
 * The message format is: v1:<timestamp>:<body>
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WebhookSignatureVerifier
{
    /**
     * The decoded secret key.
     */
    protected string $decodedSecret;

    /**
     * Create a new WebhookSignatureVerifier instance.
     *
     * @param  string  $secret  The Base64-encoded secret key from Samsara webhook configuration
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $secret)
    {
        $decoded = base64_decode($secret, true);

        if ($decoded === false || $decoded === '') {
            throw new \InvalidArgumentException('Invalid Base64-encoded webhook secret provided.');
        }

        $this->decodedSecret = $decoded;
    }

    /**
     * Compute the expected signature for a given payload and timestamp.
     *
     * @param  string  $payload  The raw request body (unmodified)
     * @param  string  $timestamp  The timestamp from X-Samsara-Timestamp header
     */
    public function computeSignature(string $payload, string $timestamp): string
    {
        $message = "v1:{$timestamp}:{$payload}";

        return 'v1='.hash_hmac('sha256', $message, $this->decodedSecret);
    }

    /**
     * Check if the signature is valid without throwing an exception.
     *
     * @param  string  $payload  The raw request body (unmodified)
     * @param  string  $signature  The signature from X-Samsara-Signature header
     * @param  string  $timestamp  The timestamp from X-Samsara-Timestamp header
     */
    public function isValid(string $payload, string $signature, string $timestamp): bool
    {
        if (empty($signature)) {
            return false;
        }

        $expectedSignature = $this->computeSignature($payload, $timestamp);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify the signature and throw an exception if invalid.
     *
     * @param  string  $payload  The raw request body (unmodified)
     * @param  string  $signature  The signature from X-Samsara-Signature header
     * @param  string  $timestamp  The timestamp from X-Samsara-Timestamp header
     *
     * @throws InvalidSignatureException
     */
    public function verify(string $payload, string $signature, string $timestamp): void
    {
        if (! $this->isValid($payload, $signature, $timestamp)) {
            throw InvalidSignatureException::invalidSignature(
                $this->computeSignature($payload, $timestamp),
                $signature
            );
        }
    }

    /**
     * Verify a webhook request including header validation and timestamp tolerance.
     *
     * This is the recommended method for verifying incoming webhook requests.
     *
     * @param  string  $payload  The raw request body (unmodified)
     * @param  string|null  $signature  The X-Samsara-Signature header value
     * @param  string|null  $timestamp  The X-Samsara-Timestamp header value
     * @param  int  $tolerance  Maximum age of the request in seconds (default: 300)
     *
     * @throws InvalidSignatureException
     */
    public function verifyFromRequest(
        string $payload,
        ?string $signature,
        ?string $timestamp,
        int $tolerance = 300
    ): void {
        if ($signature === null || $signature === '') {
            throw InvalidSignatureException::missingSignature();
        }

        if ($timestamp === null || $timestamp === '') {
            throw InvalidSignatureException::missingTimestamp();
        }

        // Check if timestamp is within tolerance
        $timestampAge = abs(time() - (int) $timestamp);

        if ($timestampAge > $tolerance) {
            throw InvalidSignatureException::expiredTimestamp($tolerance, $timestampAge);
        }

        // Verify the signature
        $this->verify($payload, $signature, $timestamp);
    }

    /**
     * Create a new WebhookSignatureVerifier instance.
     *
     * @param  string  $secret  The Base64-encoded secret key from Samsara webhook configuration
     */
    public static function make(string $secret): static
    {
        return new static($secret);
    }
}

<?php

namespace Samsara\Tests\Unit\Exceptions;

use Exception;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\InvalidSignatureException;

/**
 * Unit tests for the InvalidSignatureException class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class InvalidSignatureExceptionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_message(): void
    {
        $exception = new InvalidSignatureException('Webhook signature verification failed');

        $this->assertSame('Webhook signature verification failed', $exception->getMessage());
    }

    #[Test]
    public function it_can_be_created_with_previous_exception(): void
    {
        $previous = new Exception('HMAC calculation failed');
        $exception = new InvalidSignatureException('Invalid signature', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function it_can_be_created_with_static_make_method(): void
    {
        $exception = InvalidSignatureException::make('Invalid signature', 401);

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertSame('Invalid signature', $exception->getMessage());
        $this->assertSame(401, $exception->getCode());
    }

    #[Test]
    public function it_can_store_context_with_signature_details(): void
    {
        $context = [
            'expected'  => 'v1=abc123',
            'received'  => 'v1=xyz789',
            'timestamp' => '1640000000',
        ];

        $exception = new InvalidSignatureException('Signature mismatch');
        $exception->setContext($context);

        $this->assertSame($context, $exception->getContext());
    }

    #[Test]
    public function it_extends_samsara_exception(): void
    {
        $exception = new InvalidSignatureException('Invalid signature');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function it_has_expired_timestamp_factory_method(): void
    {
        $exception = InvalidSignatureException::expiredTimestamp(300, 600);

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertStringContainsString('expired', strtolower($exception->getMessage()));
        $this->assertSame([
            'tolerance' => 300,
            'age'       => 600,
        ], $exception->getContext());
    }

    #[Test]
    public function it_has_invalid_signature_factory_method(): void
    {
        $exception = InvalidSignatureException::invalidSignature('v1=expected', 'v1=received');

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertStringContainsString('signature', strtolower($exception->getMessage()));
        $this->assertSame([
            'expected' => 'v1=expected',
            'received' => 'v1=received',
        ], $exception->getContext());
    }

    #[Test]
    public function it_has_missing_signature_factory_method(): void
    {
        $exception = InvalidSignatureException::missingSignature();

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertStringContainsString('missing', strtolower($exception->getMessage()));
    }

    #[Test]
    public function it_has_missing_timestamp_factory_method(): void
    {
        $exception = InvalidSignatureException::missingTimestamp();

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertStringContainsString('timestamp', strtolower($exception->getMessage()));
    }

    #[Test]
    public function it_has_missing_webhook_secret_factory_method(): void
    {
        $exception = InvalidSignatureException::missingWebhookSecret();

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertStringContainsString('webhook secret not configured', strtolower($exception->getMessage()));
        $this->assertSame(500, $exception->getCode());
    }
}

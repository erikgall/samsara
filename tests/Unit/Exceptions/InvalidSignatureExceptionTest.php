<?php

namespace Samsara\Tests\Unit\Exceptions;

use Exception;
use Samsara\Tests\TestCase;
use Illuminate\Http\Request;
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
    public function it_can_be_created_with_previous_exception(): void
    {
        $previous = new Exception('HMAC calculation failed');
        $exception = new InvalidSignatureException('Invalid signature', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function it_can_be_created_with_status(): void
    {
        $exception = new InvalidSignatureException('Invalid signature', 401);

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertSame(401, $exception->getCode());
    }

    #[Test]
    public function it_can_store_context_with_signature_details(): void
    {
        $context = [
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
        $this->assertSame([
            'tolerance' => 300,
            'age'       => 600,
        ], $exception->getContext());
        $this->assertSame(401, $exception->getCode());
    }

    #[Test]
    public function it_has_future_timestamp_factory_method(): void
    {
        $exception = InvalidSignatureException::futureTimestamp(1);

        $this->assertSame(401, $exception->getCode());
        $this->assertSame(['seconds_in_future' => 1], $exception->getContext());
    }

    #[Test]
    public function it_has_invalid_signature_factory_method(): void
    {
        $exception = InvalidSignatureException::invalidSignature('v1=expected', 'v1=received');

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertSame([
            'received' => 'v1=received',
        ], $exception->getContext());
        $this->assertSame(401, $exception->getCode());
    }

    #[Test]
    public function it_has_invalid_timestamp_factory_method(): void
    {
        $exception = InvalidSignatureException::invalidTimestamp('not-a-timestamp');

        $this->assertSame(401, $exception->getCode());
        $this->assertSame(
            ['timestamp' => 'not-a-timestamp'],
            $exception->getContext()
        );
    }

    #[Test]
    public function it_has_missing_signature_factory_method(): void
    {
        $exception = InvalidSignatureException::missingSignature();

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertSame(401, $exception->getCode());
        $this->assertSame(['header' => 'X-Samsara-Signature'], $exception->getContext());
    }

    #[Test]
    public function it_has_missing_timestamp_factory_method(): void
    {
        $exception = InvalidSignatureException::missingTimestamp();

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertSame(401, $exception->getCode());
        $this->assertSame(['header' => 'X-Samsara-Timestamp'], $exception->getContext());
    }

    #[Test]
    public function it_has_missing_webhook_secret_factory_method(): void
    {
        $exception = InvalidSignatureException::missingWebhookSecret();

        $this->assertInstanceOf(InvalidSignatureException::class, $exception);
        $this->assertSame(500, $exception->getCode());
    }

    #[Test]
    public function it_renders_missing_configuration_as_server_error(): void
    {
        $response = InvalidSignatureException::missingWebhookSecret()
            ->render(Request::create('/webhook', 'POST'));

        $this->assertSame(500, $response->getStatusCode());
    }

    #[Test]
    public function it_renders_verification_failures_as_unauthorized(): void
    {
        $exception = InvalidSignatureException::invalidSignature('v1=expected', 'v1=received');
        $response = $exception->render(Request::create('/webhook', 'POST'));
        $content = $response->getContent();

        $this->assertSame(401, $response->getStatusCode());
        $this->assertIsString($content);
        $this->assertStringNotContainsString('v1=expected', $content);
        $this->assertStringNotContainsString('v1=received', $content);
    }
}

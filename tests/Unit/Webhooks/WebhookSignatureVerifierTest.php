<?php

namespace Samsara\Tests\Unit\Webhooks;

use Samsara\Tests\TestCase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Webhooks\WebhookSignatureVerifier;
use Samsara\Exceptions\InvalidSignatureException;

/**
 * Unit tests for the WebhookSignatureVerifier class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WebhookSignatureVerifierTest extends TestCase
{
    /**
     * A known request body for testing.
     */
    private const TEST_BODY = '{"event":"vehicle.location.updated","data":{}}';

    /**
     * A known Base64-encoded secret key for testing.
     */
    private const TEST_SECRET = 'rGoy+beNph0qGBLj6Aqoydj6SGA=';

    /**
     * A known timestamp for testing.
     */
    private const TEST_TIMESTAMP = '1640000000';

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::createFromTimestamp(self::TEST_TIMESTAMP));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function it_accepts_a_timestamp_exactly_at_tolerance(): void
    {
        $this->expectNotToPerformAssertions();

        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);
        $timestamp = (string) (Carbon::now()->getTimestamp() - 300);
        $signature = $verifier->computeSignature(self::TEST_BODY, $timestamp);

        $verifier->verifyFromRequest(self::TEST_BODY, $signature, $timestamp);
    }

    #[Test]
    public function it_accepts_custom_tolerance(): void
    {
        $this->expectNotToPerformAssertions();

        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);
        $timestamp = (string) (Carbon::now()->getTimestamp() - 400);
        $signature = $verifier->computeSignature(self::TEST_BODY, $timestamp);

        $verifier->verifyFromRequest(self::TEST_BODY, $signature, $timestamp, 500);
    }

    #[Test]
    public function it_accepts_the_current_timestamp_with_zero_tolerance(): void
    {
        $this->expectNotToPerformAssertions();

        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);
        $timestamp = (string) Carbon::now()->getTimestamp();
        $signature = $verifier->computeSignature(self::TEST_BODY, $timestamp);

        $verifier->verifyFromRequest(self::TEST_BODY, $signature, $timestamp, 0);
    }

    #[Test]
    public function it_can_be_created_with_static_make_method(): void
    {
        $verifier = WebhookSignatureVerifier::make(self::TEST_SECRET);

        $this->assertInstanceOf(WebhookSignatureVerifier::class, $verifier);
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        $this->assertInstanceOf(WebhookSignatureVerifier::class, $verifier);
    }

    #[Test]
    public function it_computes_correct_signature(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Manually compute expected signature using same algorithm
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':'.self::TEST_BODY;
        $expectedSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        $computedSignature = $verifier->computeSignature(self::TEST_BODY, self::TEST_TIMESTAMP);

        $this->assertSame($expectedSignature, $computedSignature);
    }

    #[Test]
    public function it_handles_empty_body(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature for empty body
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':';
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        $result = $verifier->isValid('', $validSignature, self::TEST_TIMESTAMP);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_handles_empty_signature(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        $result = $verifier->isValid(self::TEST_BODY, '', self::TEST_TIMESTAMP);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_handles_signature_without_v1_prefix(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Signature missing v1= prefix
        $result = $verifier->isValid(self::TEST_BODY, 'invalidsignature', self::TEST_TIMESTAMP);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_is_case_sensitive_for_signatures(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        // Make signature uppercase
        $uppercaseSignature = strtoupper($validSignature);

        $result = $verifier->isValid(self::TEST_BODY, $uppercaseSignature, self::TEST_TIMESTAMP);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_preserves_body_exactly_without_modification(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Test with body containing special characters that might be post-processed
        $bodyWithSpecialChars = '{"event":"test","data":{"emoji":"\\u2764","quote":"He said \\"hello\\""}}';

        // Compute signature with exact body
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':'.$bodyWithSpecialChars;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        $result = $verifier->isValid($bodyWithSpecialChars, $validSignature, self::TEST_TIMESTAMP);

        $this->assertTrue($result);
    }

    #[Test]
    #[DataProvider('missingHeaderValues')]
    public function it_rejects_a_missing_signature(?string $signature): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        try {
            $verifier->verifyFromRequest(self::TEST_BODY, $signature, self::TEST_TIMESTAMP);
            $this->fail('Expected a missing signature exception.');
        } catch (InvalidSignatureException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(['header' => 'X-Samsara-Signature'], $exception->getContext());
        }
    }

    #[Test]
    #[DataProvider('missingHeaderValues')]
    public function it_rejects_a_missing_timestamp(?string $timestamp): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        try {
            $verifier->verifyFromRequest(self::TEST_BODY, 'v1=signature', $timestamp);
            $this->fail('Expected a missing timestamp exception.');
        } catch (InvalidSignatureException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(['header' => 'X-Samsara-Timestamp'], $exception->getContext());
        }
    }

    #[Test]
    public function it_rejects_a_timestamp_one_second_in_the_future(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);
        $timestamp = (string) (Carbon::now()->getTimestamp() + 1);
        $signature = $verifier->computeSignature(self::TEST_BODY, $timestamp);

        try {
            $verifier->verifyFromRequest(self::TEST_BODY, $signature, $timestamp);
            $this->fail('Expected a future timestamp exception.');
        } catch (InvalidSignatureException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(['seconds_in_future' => 1], $exception->getContext());
        }
    }

    #[Test]
    public function it_rejects_a_timestamp_one_second_past_tolerance(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);
        $timestamp = (string) (Carbon::now()->getTimestamp() - 301);
        $signature = $verifier->computeSignature(self::TEST_BODY, $timestamp);

        try {
            $verifier->verifyFromRequest(self::TEST_BODY, $signature, $timestamp);
            $this->fail('Expected an expired timestamp exception.');
        } catch (InvalidSignatureException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(['tolerance' => 300, 'age' => 301], $exception->getContext());
        }
    }

    #[Test]
    public function it_rejects_an_invalid_signature_after_timestamp_check(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);
        $timestamp = (string) Carbon::now()->getTimestamp();

        try {
            $verifier->verifyFromRequest(self::TEST_BODY, 'v1=invalidsignature', $timestamp);
            $this->fail('Expected an invalid signature exception.');
        } catch (InvalidSignatureException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(['received' => 'v1=invalidsignature'], $exception->getContext());
        }
    }

    #[Test]
    public function it_rejects_an_invalid_timestamp_format(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        try {
            $verifier->verifyFromRequest(self::TEST_BODY, 'v1=signature', 'not-a-timestamp');
            $this->fail('Expected an invalid timestamp exception.');
        } catch (InvalidSignatureException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(['timestamp' => 'not-a-timestamp'], $exception->getContext());
        }
    }

    #[Test]
    public function it_returns_false_for_invalid_signature(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        $result = $verifier->isValid(self::TEST_BODY, 'v1=invalidsignature', self::TEST_TIMESTAMP);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_false_for_tampered_body(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature for original body
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        // Verify with tampered body
        $tamperedBody = '{"event":"vehicle.location.updated","data":{"tampered":true}}';
        $result = $verifier->isValid($tamperedBody, $validSignature, self::TEST_TIMESTAMP);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_false_for_wrong_timestamp(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature for original timestamp
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        // Verify with wrong timestamp
        $result = $verifier->isValid(self::TEST_BODY, $validSignature, '1640000001');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_true_for_valid_signature(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        $result = $verifier->isValid(self::TEST_BODY, $validSignature, self::TEST_TIMESTAMP);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_throws_exception_for_empty_secret(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new WebhookSignatureVerifier('');
    }

    #[Test]
    public function it_throws_exception_for_invalid_base64_secret(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Invalid base64 with illegal characters in strict mode
        new WebhookSignatureVerifier('!!!not-valid-base64!!!');
    }

    #[Test]
    public function it_throws_for_an_invalid_signature(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        try {
            $verifier->verify(self::TEST_BODY, 'v1=invalidsignature', self::TEST_TIMESTAMP);
            $this->fail('Expected an invalid signature exception.');
        } catch (InvalidSignatureException $exception) {
            $this->assertSame(401, $exception->getCode());
            $this->assertSame(['received' => 'v1=invalidsignature'], $exception->getContext());
        }
    }

    #[Test]
    public function it_uses_hash_equals_for_timing_safe_comparison(): void
    {
        $method = new \ReflectionMethod(WebhookSignatureVerifier::class, 'isValid');
        $filename = $method->getFileName();

        $this->assertIsString($filename);
        $source = file($filename);
        $this->assertIsArray($source);
        $methodSource = implode('', array_slice(
            $source,
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1,
        ));

        $this->assertStringContainsString('hash_equals(', $methodSource);
    }

    #[Test]
    public function it_verifies_a_valid_signature(): void
    {
        $this->expectNotToPerformAssertions();

        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);
        $signature = $verifier->computeSignature(self::TEST_BODY, self::TEST_TIMESTAMP);

        $verifier->verify(self::TEST_BODY, $signature, self::TEST_TIMESTAMP);
    }

    /**
     * @return array<string, array{string|null}>
     */
    public static function missingHeaderValues(): array
    {
        return ['absent' => [null], 'empty' => ['']];
    }
}

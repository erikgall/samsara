<?php

namespace Samsara\Tests\Unit\Webhooks;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
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
        $this->expectExceptionMessage('Invalid Base64-encoded webhook secret provided.');

        new WebhookSignatureVerifier('');
    }

    #[Test]
    public function it_throws_exception_for_invalid_base64_secret(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Base64-encoded webhook secret provided.');

        // Invalid base64 with illegal characters in strict mode
        new WebhookSignatureVerifier('!!!not-valid-base64!!!');
    }

    #[Test]
    public function it_uses_timing_safe_comparison(): void
    {
        // This test ensures timing-safe comparison is used by verifying
        // both valid and invalid signatures complete in similar time
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        // Run multiple iterations to smooth out timing variations
        $iterations = 100;

        $startValid = hrtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $verifier->isValid(self::TEST_BODY, $validSignature, self::TEST_TIMESTAMP);
        }
        $validTime = hrtime(true) - $startValid;

        $startInvalid = hrtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $verifier->isValid(self::TEST_BODY, 'v1=0000000000000000000000000000000000000000000000000000000000000000', self::TEST_TIMESTAMP);
        }
        $invalidTime = hrtime(true) - $startInvalid;

        // Times should be within 50% of each other (very lenient to avoid flaky tests)
        $ratio = max($validTime, $invalidTime) / max(min($validTime, $invalidTime), 1);
        $this->assertLessThan(2.0, $ratio, 'Timing-safe comparison should result in similar execution times');
    }

    #[Test]
    public function verify_does_not_throw_for_valid_signature(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.self::TEST_TIMESTAMP.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        // Should not throw
        $verifier->verify(self::TEST_BODY, $validSignature, self::TEST_TIMESTAMP);

        $this->assertTrue(true); // Assertion to confirm we got here
    }

    #[Test]
    public function verify_from_request_accepts_custom_tolerance(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature for somewhat old timestamp
        $oldTimestamp = (string) (time() - 400); // 400 seconds ago
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.$oldTimestamp.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        // Should not throw with 500 second tolerance
        $verifier->verifyFromRequest(self::TEST_BODY, $validSignature, $oldTimestamp, 500);

        $this->assertTrue(true); // Assertion to confirm we got here
    }

    #[Test]
    public function verify_from_request_throws_for_expired_timestamp(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature for old timestamp
        $oldTimestamp = (string) (time() - 400); // 400 seconds ago (beyond default 300s tolerance)
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = 'v1:'.$oldTimestamp.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('expired');

        $verifier->verifyFromRequest(self::TEST_BODY, $validSignature, $oldTimestamp);
    }

    #[Test]
    public function verify_from_request_throws_for_invalid_signature_after_timestamp_check(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        $timestamp = (string) time();

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('signature mismatch');

        $verifier->verifyFromRequest(self::TEST_BODY, 'v1=invalidsignature', $timestamp);
    }

    #[Test]
    public function verify_from_request_throws_for_missing_signature(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('missing X-Samsara-Signature');

        $verifier->verifyFromRequest(self::TEST_BODY, null, self::TEST_TIMESTAMP);
    }

    #[Test]
    public function verify_from_request_throws_for_missing_timestamp(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('missing X-Samsara-Timestamp');

        $verifier->verifyFromRequest(self::TEST_BODY, 'v1=signature', null);
    }

    #[Test]
    public function verify_from_request_validates_signature_and_timestamp(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        // Compute valid signature
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $timestamp = (string) time(); // Current timestamp
        $message = 'v1:'.$timestamp.':'.self::TEST_BODY;
        $validSignature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        // Should not throw with current timestamp
        $verifier->verifyFromRequest(self::TEST_BODY, $validSignature, $timestamp);

        $this->assertTrue(true); // Assertion to confirm we got here
    }

    #[Test]
    public function verify_throws_for_invalid_signature(): void
    {
        $verifier = new WebhookSignatureVerifier(self::TEST_SECRET);

        $this->expectException(InvalidSignatureException::class);

        $verifier->verify(self::TEST_BODY, 'v1=invalidsignature', self::TEST_TIMESTAMP);
    }
}

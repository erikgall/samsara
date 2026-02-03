<?php

namespace Samsara\Tests\Unit\Webhooks;

use Samsara\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Webhooks\VerifyWebhookSignature;
use Samsara\Exceptions\InvalidSignatureException;

/**
 * Unit tests for the VerifyWebhookSignature middleware.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VerifyWebhookSignatureTest extends TestCase
{
    /**
     * A known request body for testing.
     */
    private const TEST_BODY = '{"event":"vehicle.location.updated","data":{}}';

    /**
     * A known Base64-encoded secret key for testing.
     */
    private const TEST_SECRET = 'rGoy+beNph0qGBLj6Aqoydj6SGA=';

    #[Test]
    public function it_accepts_custom_secret_via_constructor(): void
    {
        $customSecret = base64_encode('custom-secret');
        $middleware = new VerifyWebhookSignature($customSecret);

        $timestamp = (string) time();
        $decodedSecret = base64_decode($customSecret);
        $message = "v1:{$timestamp}:".self::TEST_BODY;
        $signature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $timestamp,
        ], self::TEST_BODY);

        $response = $middleware->handle($request, function () {
            return new Response('OK', 200);
        });

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function it_accepts_custom_tolerance(): void
    {
        $middleware = new VerifyWebhookSignature(self::TEST_SECRET, 500);

        $oldTimestamp = (string) (time() - 400); // 400 seconds ago, within 500s tolerance
        $signature = $this->createValidSignature(self::TEST_BODY, $oldTimestamp);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $oldTimestamp,
        ], self::TEST_BODY);

        $response = $middleware->handle($request, function () {
            return new Response('OK', 200);
        });

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function it_accepts_secret_via_route_middleware_parameter(): void
    {
        $middleware = new VerifyWebhookSignature;

        $routeSecret = base64_encode('route-param-secret');
        $timestamp = (string) time();
        $decodedSecret = base64_decode($routeSecret);
        $message = "v1:{$timestamp}:".self::TEST_BODY;
        $signature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $timestamp,
        ], self::TEST_BODY);

        // Pass secret as route middleware parameter (third argument)
        $response = $middleware->handle($request, function () {
            return new Response('OK', 200);
        }, $routeSecret);

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function it_accepts_tolerance_via_route_middleware_parameter(): void
    {
        $middleware = new VerifyWebhookSignature;

        $oldTimestamp = (string) (time() - 400); // 400 seconds ago
        $signature = $this->createValidSignature(self::TEST_BODY, $oldTimestamp);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $oldTimestamp,
        ], self::TEST_BODY);

        // Pass tolerance as route middleware parameter (fourth argument as string, simulating route params)
        $response = $middleware->handle($request, function () {
            return new Response('OK', 200);
        }, self::TEST_SECRET, '500');

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function it_allows_request_with_valid_signature(): void
    {
        $middleware = new VerifyWebhookSignature;
        $timestamp = (string) time();
        $signature = $this->createValidSignature(self::TEST_BODY, $timestamp);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $timestamp,
        ], self::TEST_BODY);

        $response = $middleware->handle($request, function () {
            return new Response('OK', 200);
        });

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getContent());
    }

    #[Test]
    public function it_can_be_instantiated_with_static_make_method(): void
    {
        $middleware = VerifyWebhookSignature::make(self::TEST_SECRET);

        $this->assertInstanceOf(VerifyWebhookSignature::class, $middleware);
    }

    #[Test]
    public function it_reads_raw_body_content(): void
    {
        $middleware = new VerifyWebhookSignature;

        // Body with special characters that should not be post-processed
        $bodyWithSpecialChars = '{"event":"test","emoji":"\\u2764"}';
        $timestamp = (string) time();
        $signature = $this->createValidSignature($bodyWithSpecialChars, $timestamp);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $timestamp,
        ], $bodyWithSpecialChars);

        $response = $middleware->handle($request, function () {
            return new Response('OK', 200);
        });

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function it_rejects_request_with_expired_timestamp(): void
    {
        $middleware = new VerifyWebhookSignature;
        $oldTimestamp = (string) (time() - 400); // 400 seconds ago
        $signature = $this->createValidSignature(self::TEST_BODY, $oldTimestamp);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $oldTimestamp,
        ], self::TEST_BODY);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('expired');

        $middleware->handle($request, function () {
            return new Response('OK', 200);
        });
    }

    #[Test]
    public function it_rejects_request_with_invalid_signature(): void
    {
        $middleware = new VerifyWebhookSignature;
        $timestamp = (string) time();

        $request = $this->createRequest([
            'X-Samsara-Signature' => 'v1=invalidsignature',
            'X-Samsara-Timestamp' => $timestamp,
        ], self::TEST_BODY);

        $this->expectException(InvalidSignatureException::class);

        $middleware->handle($request, function () {
            return new Response('OK', 200);
        });
    }

    #[Test]
    public function it_rejects_request_with_missing_signature_header(): void
    {
        $middleware = new VerifyWebhookSignature;
        $timestamp = (string) time();

        $request = $this->createRequest([
            'X-Samsara-Timestamp' => $timestamp,
        ], self::TEST_BODY);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('missing X-Samsara-Signature');

        $middleware->handle($request, function () {
            return new Response('OK', 200);
        });
    }

    #[Test]
    public function it_rejects_request_with_missing_timestamp_header(): void
    {
        $middleware = new VerifyWebhookSignature;

        $request = $this->createRequest([
            'X-Samsara-Signature' => 'v1=somesignature',
        ], self::TEST_BODY);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('missing X-Samsara-Timestamp');

        $middleware->handle($request, function () {
            return new Response('OK', 200);
        });
    }

    #[Test]
    public function it_rejects_tampered_body(): void
    {
        $middleware = new VerifyWebhookSignature;

        $timestamp = (string) time();
        // Create signature for original body
        $signature = $this->createValidSignature(self::TEST_BODY, $timestamp);

        // But send tampered body
        $tamperedBody = '{"event":"vehicle.location.updated","data":{"tampered":true}}';
        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $timestamp,
        ], $tamperedBody);

        $this->expectException(InvalidSignatureException::class);

        $middleware->handle($request, function () {
            return new Response('OK', 200);
        });
    }

    #[Test]
    public function it_throws_when_webhook_secret_not_configured(): void
    {
        // Set config to null to simulate missing configuration
        config(['samsara.webhook_secret' => null]);

        $middleware = new VerifyWebhookSignature;

        $request = $this->createRequest([
            'X-Samsara-Signature' => 'v1=somesignature',
            'X-Samsara-Timestamp' => (string) time(),
        ], self::TEST_BODY);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Webhook secret not configured');

        $middleware->handle($request, function () {
            return new Response('OK', 200);
        });
    }

    #[Test]
    public function it_uses_config_secret_when_not_provided(): void
    {
        $middleware = new VerifyWebhookSignature;

        $timestamp = (string) time();
        $signature = $this->createValidSignature(self::TEST_BODY, $timestamp);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $timestamp,
        ], self::TEST_BODY);

        // This test verifies the middleware uses config('samsara.webhook_secret')
        $response = $middleware->handle($request, function () {
            return new Response('OK', 200);
        });

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function route_params_take_priority_over_constructor_params(): void
    {
        // Create middleware with one secret via constructor
        $constructorSecret = base64_encode('constructor-secret');
        $middleware = new VerifyWebhookSignature($constructorSecret, 100);

        // But use a different secret for the signature (route param secret)
        $routeSecret = base64_encode('route-param-secret');
        $timestamp = (string) time();
        $decodedSecret = base64_decode($routeSecret);
        $message = "v1:{$timestamp}:".self::TEST_BODY;
        $signature = 'v1='.hash_hmac('sha256', $message, $decodedSecret);

        $request = $this->createRequest([
            'X-Samsara-Signature' => $signature,
            'X-Samsara-Timestamp' => $timestamp,
        ], self::TEST_BODY);

        // Route param should override constructor param
        $response = $middleware->handle($request, function () {
            return new Response('OK', 200);
        }, $routeSecret);

        $this->assertSame(200, $response->getStatusCode());
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('samsara.webhook_secret', self::TEST_SECRET);
    }

    /**
     * Create a mock request with the given headers and body.
     *
     * @param  array<string, string>  $headers
     */
    private function createRequest(array $headers, string $body): Request
    {
        $request = Request::create('/webhook', 'POST', [], [], [], [], $body);

        foreach ($headers as $key => $value) {
            $request->headers->set($key, $value);
        }

        return $request;
    }

    /**
     * Create a valid signature for the test body and timestamp.
     */
    private function createValidSignature(string $body, string $timestamp): string
    {
        $decodedSecret = base64_decode(self::TEST_SECRET);
        $message = "v1:{$timestamp}:{$body}";

        return 'v1='.hash_hmac('sha256', $message, $decodedSecret);
    }
}

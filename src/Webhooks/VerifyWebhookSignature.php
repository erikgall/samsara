<?php

namespace Samsara\Webhooks;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Samsara\Exceptions\InvalidSignatureException;

/**
 * Middleware to verify Samsara webhook signatures.
 *
 * This middleware verifies that incoming webhook requests are authentic
 * by validating the HMAC-SHA256 signature in the X-Samsara-Signature header.
 *
 * Usage in routes (using config secret):
 *
 *     Route::post('/webhook', WebhookController::class)
 *         ->middleware(VerifyWebhookSignature::class);
 *
 * With custom secret via route parameters:
 *
 *     Route::post('/webhook', WebhookController::class)
 *         ->middleware('samsara.webhook:rGoy+beNph0qGBLj6Aqoydj6SGA=,600');
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VerifyWebhookSignature
{
    /**
     * The webhook secret key (Base64-encoded).
     */
    protected ?string $secret;

    /**
     * The timestamp tolerance in seconds.
     */
    protected int $tolerance;

    /**
     * Create a new middleware instance.
     *
     * @param  string|null  $secret  The Base64-encoded secret key (defaults to config)
     * @param  int  $tolerance  Maximum age of the request in seconds (default: 300)
     */
    public function __construct(?string $secret = null, int $tolerance = 300)
    {
        $this->secret = $secret;
        $this->tolerance = $tolerance;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     * @param  string|null  $secret  Optional secret passed via route middleware parameters
     * @param  int|string|null  $tolerance  Optional tolerance passed via route middleware parameters
     *
     * @throws InvalidSignatureException
     * @throws \InvalidArgumentException
     */
    public function handle(
        Request $request,
        Closure $next,
        ?string $secret = null,
        int|string|null $tolerance = null
    ): Response {
        // Priority: route params > constructor > config
        $webhookSecret = $secret ?: $this->secret ?: config('samsara.webhook_secret');
        $timestampTolerance = filled($tolerance) ? (int) $tolerance : $this->tolerance;

        if (blank($webhookSecret)) {
            throw InvalidSignatureException::missingWebhookSecret();
        }

        $verifier = new WebhookSignatureVerifier($webhookSecret);

        $verifier->verifyFromRequest(
            $request->getContent(),
            $request->headers->get('X-Samsara-Signature'),
            $request->headers->get('X-Samsara-Timestamp'),
            $timestampTolerance
        );

        return $next($request);
    }
}

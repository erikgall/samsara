---
title: Webhooks
nav_order: 16
description: Manage webhooks for real-time event notifications and verify their signatures.
permalink: /resources/webhooks
---

# Webhooks

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Webhook Events](#webhook-events)
- [Webhook Security](#webhook-security)
  - [Using The Middleware](#using-the-middleware)
  - [Using The Verifier Class](#using-the-verifier-class)
  - [Boolean Validation](#boolean-validation)
  - [Computing Signatures For Tests](#computing-signatures-for-tests)
  - [Signature Headers](#signature-headers)
  - [Timestamp Tolerance](#timestamp-tolerance)
- [Example Webhook Handler](#example-webhook-handler)
- [Route Registration](#route-registration)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Webhooks deliver Samsara events to your application as POST requests, signed with HMAC-SHA256. Use this resource to manage the webhook configuration roster (URL, event subscriptions, status). Use [`WebhookSignatureVerifier`](#using-the-verifier-class) and the bundled middleware to verify incoming requests in your handler.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$webhooks = Samsara::webhooks()->all();

$webhook = Samsara::webhooks()->find('webhook-id');
```

## Creating Records

```php
$webhook = Samsara::webhooks()->create([
    'name' => 'Fleet Events',
    'url' => 'https://example.com/webhook',
    'eventTypes' => ['VehicleCreated', 'DriverCreated'],
]);
```

## Updating Records

```php
$webhook = Samsara::webhooks()->update('webhook-id', [
    'name' => 'Updated Webhook',
]);
```

## Deleting Records

```php
Samsara::webhooks()->delete('webhook-id');
```

## Filtering

The resource exposes `query()` for filter chaining. See [Query Builder](../query-builder.md) for the full filter list.

```php
$webhooks = Samsara::webhooks()
    ->query()
    ->limit(50)
    ->get();
```

## Webhook Events

The `WebhookEvent` enum lists every supported event type. Pass the case via `->value` (the API expects the wire string).

```php
use Samsara\Enums\WebhookEvent;

$webhook = Samsara::webhooks()->create([
    'name' => 'Fleet Events',
    'url' => 'https://example.com/webhook',
    'eventTypes' => [
        WebhookEvent::VEHICLE_CREATED->value,
        WebhookEvent::DRIVER_CREATED->value,
        WebhookEvent::GEOFENCE_ENTRY->value,
    ],
]);
```

The 27 supported events grouped by domain:

| Domain | Events |
|--------|--------|
| Address | `AddressCreated`, `AddressUpdated`, `AddressDeleted` |
| Driver | `DriverCreated`, `DriverUpdated` |
| Vehicle | `VehicleCreated`, `VehicleUpdated` |
| Dispatch / Route | `RouteStopArrival`, `RouteStopDeparture`, `RouteStopEarlyLateArrival`, `RouteStopEtaUpdated`, `RouteStopResequence` |
| Safety | `SevereSpeedingStarted`, `SevereSpeedingEnded`, `SpeedingEventStarted`, `SpeedingEventEnded` |
| Geofence | `GeofenceEntry`, `GeofenceExit` |
| Industrial / Equipment | `EngineFaultOn`, `EngineFaultOff`, `GatewayUnplugged` |
| Maintenance | `DvirSubmitted`, `PredictiveMaintenanceAlert` |
| Document / Form | `DocumentSubmitted`, `FormSubmitted`, `FormUpdated` |
| Issues | `IssueCreated` |

See [Enums](../enums.md#webhookevent) for the full case-to-value reference.

## Webhook Security

Samsara signs webhook payloads with HMAC-SHA256 using a Base64-encoded secret. The SDK provides middleware and a verifier class to validate the signature.

### Using The Middleware

The simplest path is to apply `VerifyWebhookSignature` to your route. The middleware reads the secret from `config('samsara.webhook_secret')`.

```php
use Samsara\Webhooks\VerifyWebhookSignature;

Route::post('/webhooks/samsara', [SamsaraWebhookController::class, 'handle'])
    ->middleware(VerifyWebhookSignature::class);
```

Set the secret in your `.env`:

```env
SAMSARA_WEBHOOK_SECRET=your-base64-encoded-secret
```

### Using The Verifier Class

For manual verification — for example, when you want to log or branch instead of aborting — use `WebhookSignatureVerifier` directly. `verifyFromRequest()` validates the headers and timestamp tolerance, then throws `InvalidSignatureException` on failure.

```php
use Samsara\Webhooks\WebhookSignatureVerifier;
use Samsara\Exceptions\InvalidSignatureException;
use Illuminate\Http\Request;

public function handle(Request $request)
{
    $verifier = new WebhookSignatureVerifier(config('samsara.webhook_secret'));

    try {
        $verifier->verifyFromRequest(
            $request->getContent(),
            $request->header('X-Samsara-Signature'),
            $request->header('X-Samsara-Timestamp')
        );
    } catch (InvalidSignatureException $e) {
        abort(401, 'Invalid webhook signature');
    }

    $event = $request->json();
    // Dispatch the event
}
```

`verify()` is the lower-level variant: it accepts the payload, signature, and timestamp directly without validating headers.

```php
$verifier->verify($payload, $signature, $timestamp);
```

See [Error Handling](../error-handling.md) for details on `InvalidSignatureException`.

### Boolean Validation

When you would rather branch than catch an exception, use `isValid()`. It returns `true` when the signature matches and `false` otherwise — including when the signature string is empty.

```php
if (! $verifier->isValid($payload, $signature, $timestamp)) {
    return response()->json(['error' => 'Invalid signature'], 401);
}
```

### Computing Signatures For Tests

`computeSignature()` returns the expected `v1=<hexdigest>` string for a payload and timestamp. Use it to sign synthetic requests in feature tests.

```php
$verifier = new WebhookSignatureVerifier(config('samsara.webhook_secret'));

$payload = json_encode(['eventType' => 'VehicleCreated']);
$timestamp = (string) time();
$signature = $verifier->computeSignature($payload, $timestamp);

$this->postJson('/webhooks/samsara', json_decode($payload, true), [
    'X-Samsara-Signature' => $signature,
    'X-Samsara-Timestamp' => $timestamp,
]);
```

### Signature Headers

| Header | Description |
|--------|-------------|
| `X-Samsara-Signature` | Signature in `v1=<hexdigest>` format. |
| `X-Samsara-Timestamp` | Unix timestamp when the request was sent. |

### Timestamp Tolerance

`verifyFromRequest()` rejects requests older than 300 seconds (5 minutes) by default. Pass a custom tolerance as the fourth argument.

```php
// Middleware with a 10-minute tolerance
Route::post('/webhooks/samsara', SamsaraWebhookController::class)
    ->middleware(new VerifyWebhookSignature(null, 600));

// Or directly on the verifier
$verifier->verifyFromRequest($payload, $signature, $timestamp, 600);
```

## Example Webhook Handler

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SamsaraWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $eventType = $request->input('eventType');
        $data = $request->input('data');

        match ($eventType) {
            'VehicleCreated' => $this->handleVehicleCreated($data),
            'DriverCreated' => $this->handleDriverCreated($data),
            'GeofenceEntry' => $this->handleGeofenceEntry($data),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    protected function handleVehicleCreated(array $data): void
    {
        // Process vehicle created event
    }

    protected function handleDriverCreated(array $data): void
    {
        // Process driver created event
    }

    protected function handleGeofenceEntry(array $data): void
    {
        // Process geofence entry event
    }
}
```

## Route Registration

Register the route under `routes/api.php` (the recommended path — Laravel does not apply CSRF middleware to API routes).

```php
use Samsara\Webhooks\VerifyWebhookSignature;

Route::post('/webhooks/samsara', [SamsaraWebhookController::class, 'handle'])
    ->middleware(VerifyWebhookSignature::class);
```

> **Note:** If you must register the webhook under `routes/web.php`, exclude it from CSRF protection via `bootstrap/app.php` rather than the route. On Laravel 12+ the supported pattern is `->withMiddleware(function (Middleware $middleware) { $middleware->validateCsrfTokens(except: ['webhooks/samsara']); })`. Per-route disabling via `withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)` also works but is discouraged.

## Helper Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `isActive()` | `bool` | True when `status === 'active'`. |
| `isPaused()` | `bool` | True when `status === 'paused'`. |
| `hasEventType(string $eventType)` | `bool` | True when the webhook subscribes to the given event. |

```php
$webhook = Samsara::webhooks()->find('webhook-id');

if ($webhook->isPaused()) {
    Samsara::webhooks()->update($webhook->id, ['status' => 'active']);
}

if ($webhook->hasEventType('VehicleCreated')) {
    // Already subscribed
}
```

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Webhook id. |
| `name` | `?string` | Webhook name. |
| `url` | `?string` | The destination URL Samsara posts events to. |
| `secret` | `?string` | Base64-encoded HMAC secret (returned on create only). |
| `status` | `?string` | `"active"` or `"paused"`. |
| `eventTypes` | `?array<int, string>` | Subscribed event type wire values. |
| `customHeaders` | `?array` | Additional headers Samsara appends to each delivery. Each item exposes `name` and `value`. |
| `createdAtTime` | `?string` | RFC 3339 creation timestamp. |
| `updatedAtTime` | `?string` | RFC 3339 last-update timestamp. |

## Related Resources

- [Error Handling](../error-handling.md) — `InvalidSignatureException` reference.
- [Enums](../enums.md#webhookevent) — full `WebhookEvent` case list.
- [Testing](../testing.md) — patterns for asserting webhook delivery in your test suite.

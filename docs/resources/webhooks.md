---
title: Webhooks
layout: default
parent: Resources
nav_order: 16
description: "Manage webhooks for real-time event notifications"
permalink: /resources/webhooks
---

# Webhooks Resource

Manage webhooks for real-time event notifications.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all webhooks
$webhooks = Samsara::webhooks()->all();

// Find a webhook
$webhook = Samsara::webhooks()->find('webhook-id');

// Create a webhook
$webhook = Samsara::webhooks()->create([
    'name' => 'My Webhook',
    'url' => 'https://example.com/webhook',
    'eventTypes' => ['VehicleLocation', 'DriverHosLog'],
]);

// Update a webhook
$webhook = Samsara::webhooks()->update('webhook-id', [
    'name' => 'Updated Webhook',
]);

// Delete a webhook
Samsara::webhooks()->delete('webhook-id');
```

## Webhook Events

Use the `WebhookEvent` enum for available event types:

```php
use Samsara\Enums\WebhookEvent;

$webhook = Samsara::webhooks()->create([
    'name' => 'Fleet Events',
    'url' => 'https://example.com/webhook',
    'eventTypes' => [
        WebhookEvent::VEHICLE_LOCATION->value,
        WebhookEvent::DRIVER_HOS_LOG->value,
        WebhookEvent::DRIVER_CREATED->value,
    ],
]);
```

### Available Event Types

#### Driver Events
- `DriverCreated`
- `DriverUpdated`
- `DriverHosLog`

#### Vehicle Events
- `VehicleCreated`
- `VehicleUpdated`
- `VehicleLocation`

#### Route Events
- `RouteCreated`
- `RouteUpdated`
- `RouteDeleted`
- `RouteStopArrival`
- `RouteStopDeparture`

#### Safety Events
- `SafetyEvent`
- `CameraSafetyEvent`

#### Maintenance Events
- `DvirCreated`
- `DvirUpdated`
- `DefectCreated`
- `DefectUpdated`

#### Document Events
- `DocumentSubmitted`

#### Alert Events
- `AlertIncident`

See the `WebhookEvent` enum for 70+ available event types.

## Webhook Entity

```php
$webhook = Samsara::webhooks()->find('webhook-id');

$webhook->id;         // string
$webhook->name;       // string
$webhook->url;        // string
$webhook->eventTypes; // array
$webhook->enabled;    // bool
$webhook->secret;     // ?string
```

## Webhook Security

Samsara signs webhook payloads using HMAC-SHA256. The SDK provides a middleware and verifier class to validate signatures.

### Using the Middleware (Recommended)

The easiest way to verify webhook signatures is with the `VerifyWebhookSignature` middleware:

```php
// routes/api.php
use Samsara\Webhooks\VerifyWebhookSignature;

Route::post('/webhooks/samsara', [SamsaraWebhookController::class, 'handle'])
    ->middleware(VerifyWebhookSignature::class);
```

The middleware reads the secret from `config('samsara.webhook_secret')`. Add it to your `.env`:

```env
SAMSARA_WEBHOOK_SECRET=your-base64-encoded-secret
```

### Using the Verifier Class

For manual verification or custom logic, use the `WebhookSignatureVerifier` class:

```php
use Samsara\Webhooks\WebhookSignatureVerifier;
use Samsara\Exceptions\InvalidSignatureException;

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

    // Process webhook
    $event = $request->json();
    // ...
}
```

### Signature Headers

Samsara sends two headers with each webhook request:

| Header | Description |
|--------|-------------|
| `X-Samsara-Signature` | The signature in format `v1=<hexdigest>` |
| `X-Samsara-Timestamp` | Unix timestamp when the request was sent |

### Timestamp Tolerance

By default, the verifier rejects requests older than 300 seconds (5 minutes). Customize this:

```php
// Middleware with custom tolerance (10 minutes)
Route::post('/webhooks/samsara', WebhookController::class)
    ->middleware(new VerifyWebhookSignature(null, 600));

// Or with the verifier class
$verifier->verifyFromRequest($payload, $signature, $timestamp, 600);
```

## Example Webhook Handler

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SamsaraWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // Signature is already verified by middleware
        $eventType = $request->input('eventType');
        $data = $request->input('data');

        match ($eventType) {
            'VehicleLocation' => $this->handleVehicleLocation($data),
            'DriverHosLog' => $this->handleDriverHosLog($data),
            'SafetyEvent' => $this->handleSafetyEvent($data),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    protected function handleVehicleLocation(array $data): void
    {
        // Process vehicle location update
    }

    protected function handleDriverHosLog(array $data): void
    {
        // Process HOS log update
    }

    protected function handleSafetyEvent(array $data): void
    {
        // Process safety event
    }
}
```

## Route Registration

```php
// routes/api.php
use Samsara\Webhooks\VerifyWebhookSignature;

Route::post('/webhooks/samsara', [SamsaraWebhookController::class, 'handle'])
    ->middleware(VerifyWebhookSignature::class);
```

> **Note:** When using the `api` routes, CSRF protection is not applied. If using `web.php`, exclude CSRF with `->withoutMiddleware(['csrf'])`.

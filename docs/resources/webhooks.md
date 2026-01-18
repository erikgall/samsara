# Webhooks Resource

Manage webhooks for real-time event notifications.

## Basic Usage

```php
use ErikGall\Samsara\Facades\Samsara;

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
use ErikGall\Samsara\Enums\WebhookEvent;

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

Samsara signs webhook payloads. Verify signatures in your webhook handler:

```php
// In your webhook controller
public function handle(Request $request)
{
    $signature = $request->header('Samsara-Signature');
    $payload = $request->getContent();
    $secret = config('services.samsara.webhook_secret');

    $expected = hash_hmac('sha256', $payload, $secret);

    if (!hash_equals($expected, $signature)) {
        abort(401, 'Invalid signature');
    }

    // Process webhook
    $event = $request->json();
    // ...
}
```

## Example Webhook Handler

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SamsaraWebhookController extends Controller
{
    public function handle(Request $request)
    {
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
// routes/web.php
Route::post('/webhooks/samsara', [SamsaraWebhookController::class, 'handle'])
    ->withoutMiddleware(['csrf']);
```

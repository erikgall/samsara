---
title: Legacy Resources
nav_order: 92
description: Legacy v1 Samsara endpoints surfaced through the SDK.
permalink: /resources/legacy
---

# Legacy Resources

> **Warning:** Legacy endpoints proxy v1 Samsara API endpoints that have not been migrated to v2. Prefer the v2 equivalent on the corresponding stable resource page when one exists. Each method returns the raw v1 response array.

- [Introduction](#introduction)
- [Method Reference](#method-reference)
- [Related Resources](#related-resources)

## Introduction

Legacy resources expose v1 Samsara endpoints that have no v2 replacement, or that you may still need to call when a v2 migration is incomplete. Reach them through `Samsara::legacy()`, which returns a `LegacyResource` instance. Every method returns the raw decoded JSON response (`array`); `visionCameras()` returns a list rather than a typed entity. When a stable v2 resource covers the same data, prefer it.

The methods below use the verbs and paths declared by the pinned first-party OpenAPI snapshot. The former `dispatchRoutes()` helper was removed because its collection path does not exist in either the pinned or current live contract.

## Method Reference

### `assetLocations(array $data): array`

`GET /v1/fleet/assets/locations`. Returns the raw v1 response. Prefer the v2 equivalent on [Assets](assets.html).

```php
use Samsara\Facades\Samsara;

$payload = Samsara::legacy()->assetLocations([]);
```

### `assetReefers(array $data): array`

`GET /v1/fleet/assets/reefers`. Returns the raw v1 response. Prefer the v2 equivalent on [Assets](assets.html).

```php
$payload = Samsara::legacy()->assetReefers([
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `driverSafetyScore(string $driverId, array $params): array`

`GET /v1/fleet/drivers/{driverId}/safety/score`. Returns the raw v1 response. No v2 equivalent in the SDK.

```php
$payload = Samsara::legacy()->driverSafetyScore('driver-id', [
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `fleetAssets(array $data): array`

`GET /v1/fleet/assets`. Returns the raw v1 response. Prefer the v2 equivalent on [Assets](assets.html).

```php
$payload = Samsara::legacy()->fleetAssets([]);
```

### `hosAuthenticationLogs(array $data): array`

`GET /v1/fleet/hos_authentication_logs`. Returns the raw v1 response. No v2 equivalent in the SDK.

```php
$payload = Samsara::legacy()->hosAuthenticationLogs([
    'driverId' => 123,
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `machines(array $data): array`

`POST /v1/machines/list`. Returns the raw v1 response. No v2 equivalent in the SDK.

```php
$payload = Samsara::legacy()->machines([]);
```

### `maintenanceList(array $data): array`

`GET /v1/fleet/maintenance/list`. Returns the raw v1 response. Prefer the v2 equivalent on [Maintenance](maintenance.html).

```php
$payload = Samsara::legacy()->maintenanceList([]);
```

### `messages(array $data): array`

`GET /v1/fleet/messages`. Returns message history as a raw v1 response. This method does not send messages.

```php
$payload = Samsara::legacy()->messages(['endMs' => 1714608000000, 'durationMs' => 86_400_000]);
```

`messages()` is the GET read operation. Send a message through the distinct POST helper:

```php
$result = Samsara::legacy()->sendMessage([
    'driverIds' => [111, 222],
    'text' => 'Dispatch update',
]);
```

### `trips(array $data): array`

`GET /v1/fleet/trips`. Returns the raw v1 response. Prefer the v2 equivalent on [Trips](trips.html).

```php
$payload = Samsara::legacy()->trips([
    'vehicleId' => 123,
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `vehicleHarshEvent(string $vehicleId, array $params): array`

`GET /v1/fleet/vehicles/{vehicleId}/safety/harsh_event`. Returns the raw v1 response. Prefer the v2 equivalent on [Safety Events](safety-events.html).

```php
$payload = Samsara::legacy()->vehicleHarshEvent('vehicle-id', [
    'timestamp' => 1714521600000,
]);
```

### `vehicleSafetyScore(string $vehicleId, array $params): array`

`GET /v1/fleet/vehicles/{vehicleId}/safety/score`. Returns the raw v1 response. No v2 equivalent in the SDK.

```php
$payload = Samsara::legacy()->vehicleSafetyScore('vehicle-id', [
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `visionCameras(array $data): array`

`GET /v1/industrial/vision/cameras`. Returns the raw v1 response. Prefer the v2 equivalent on [Camera Media](camera-media.html).

```php
$payload = Samsara::legacy()->visionCameras([]);
```

## Related Resources

- [Resources Index](index.html) — full list of stable resources.
- [Assets](assets.html) — v2 equivalent for asset locations, reefers, and fleet assets.
- [Maintenance](maintenance.html) — v2 equivalent for maintenance lists.
- [Trips](trips.html) — v2 equivalent for trip data.
- [Safety Events](safety-events.html) — v2 equivalent for harsh-event reads.
- [Camera Media](camera-media.html) — v2 equivalent for vision-camera reads.

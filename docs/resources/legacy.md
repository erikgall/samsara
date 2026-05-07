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

Legacy resources expose v1 Samsara endpoints that have no v2 replacement, or that you may still need to call when a v2 migration is incomplete. Reach them through `Samsara::legacy()`, which returns a `LegacyResource` instance. Every method returns the raw decoded JSON response (`array<string, mixed>`) rather than a typed entity. When a stable v2 resource covers the same data, prefer it.

## Method Reference

### `assetLocations(array $data): array`

`POST /v1/fleet/assets/locations`. Returns the raw v1 response. Prefer the v2 equivalent on [Assets](assets.md).

```php
use Samsara\Facades\Samsara;

$payload = Samsara::legacy()->assetLocations([
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `assetReefers(array $data): array`

`POST /v1/fleet/assets/reefers`. Returns the raw v1 response. Prefer the v2 equivalent on [Assets](assets.md).

```php
$payload = Samsara::legacy()->assetReefers([
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `dispatchRoutes(array $data): array`

`GET /v1/fleet/dispatch/routes`. Returns the raw v1 response. No v2 equivalent in the SDK.

```php
$payload = Samsara::legacy()->dispatchRoutes([
    'group_id' => 'group-id',
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

`POST /v1/fleet/assets`. Returns the raw v1 response. Prefer the v2 equivalent on [Assets](assets.md).

```php
$payload = Samsara::legacy()->fleetAssets([
    'groupId' => 'group-id',
]);
```

### `hosAuthenticationLogs(array $data): array`

`POST /v1/fleet/hos_authentication_logs`. Returns the raw v1 response. No v2 equivalent in the SDK.

```php
$payload = Samsara::legacy()->hosAuthenticationLogs([
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `machines(array $data): array`

`POST /v1/machines/list`. Returns the raw v1 response. No v2 equivalent in the SDK.

```php
$payload = Samsara::legacy()->machines([
    'groupId' => 'group-id',
]);
```

### `maintenanceList(array $data): array`

`POST /v1/fleet/maintenance/list`. Returns the raw v1 response. Prefer the v2 equivalent on [Maintenance](maintenance.md).

```php
$payload = Samsara::legacy()->maintenanceList([
    'groupId' => 'group-id',
]);
```

### `messages(array $data): array`

`POST /v1/fleet/messages`. Returns the raw v1 response. No v2 equivalent in the SDK.

```php
$payload = Samsara::legacy()->messages([
    'groupId' => 'group-id',
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `trips(array $data): array`

`POST /v1/fleet/trips`. Returns the raw v1 response. Prefer the v2 equivalent on [Trips](trips.md).

```php
$payload = Samsara::legacy()->trips([
    'groupId' => 'group-id',
    'startMs' => 1714521600000,
    'endMs' => 1714608000000,
]);
```

### `vehicleHarshEvent(string $vehicleId, array $params): array`

`GET /v1/fleet/vehicles/{vehicleId}/safety/harsh_event`. Returns the raw v1 response. Prefer the v2 equivalent on [Safety Events](safety-events.md).

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

`POST /v1/industrial/vision/cameras`. Returns the raw v1 response. Prefer the v2 equivalent on [Camera Media](camera-media.md).

```php
$payload = Samsara::legacy()->visionCameras([
    'groupId' => 'group-id',
]);
```

## Related Resources

- [Resources Index](index.md) — full list of stable resources.
- [Assets](assets.md) — v2 equivalent for asset locations, reefers, and fleet assets.
- [Maintenance](maintenance.md) — v2 equivalent for maintenance lists.
- [Trips](trips.md) — v2 equivalent for trip data.
- [Safety Events](safety-events.md) — v2 equivalent for harsh-event reads.
- [Camera Media](camera-media.md) — v2 equivalent for vision-camera reads.

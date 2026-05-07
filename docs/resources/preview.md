---
title: Preview Resources
nav_order: 91
description: Preview Samsara endpoints surfaced through the SDK.
permalink: /resources/preview
---

# Preview Resources

> **Warning:** Preview endpoints are early-access features that may change before they reach general availability. Treat method signatures and response shapes as unstable.

- [Introduction](#introduction)
- [Method Reference](#method-reference)
- [Related Resources](#related-resources)

## Introduction

Preview resources expose Samsara API endpoints that are still in early testing. Reach them through `Samsara::preview()`, which returns a `PreviewResource` instance. Each method returns a raw `array<string, mixed>` rather than a typed entity. The `lockVehicle()` and `unlockVehicle()` methods are operationally critical — they immobilize and re-enable vehicles in the field — so record an audit trail (who invoked the call, when, and against which `vehicleId`) before you wire them into automated workflows.

## Method Reference

### `createDriverAuthToken(string $driverId, array $data = []): array`

Mints a driver authentication token against `/fleet/drivers/{driverId}/auth-token`. Returns the decoded `data` payload.

```php
use Samsara\Facades\Samsara;

$token = Samsara::preview()->createDriverAuthToken('driver-id', [
    'expiresAt' => '2026-06-01T00:00:00Z',
]);
```

### `lockVehicle(string $vehicleId): array`

Sends a vehicle-immobilizer lock command to `/fleet/vehicles/{vehicleId}/lock`. Returns the decoded `data` payload. Log the actor, timestamp, and `vehicleId` with each call.

```php
$response = Samsara::preview()->lockVehicle('vehicle-id');
```

### `unlockVehicle(string $vehicleId): array`

Sends a vehicle-immobilizer unlock command to `/fleet/vehicles/{vehicleId}/unlock`. Returns the decoded `data` payload. Log the actor, timestamp, and `vehicleId` with each call.

```php
$response = Samsara::preview()->unlockVehicle('vehicle-id');
```

## Related Resources

- [Resources Index](index.md) — full list of stable resources.
- [Beta Resources](beta.md) — in-development endpoints, including `vehicleImmobilizerStream()`.
- [Drivers](drivers.md) — manage the drivers referenced by `createDriverAuthToken()`.
- [Vehicles](vehicles.md) — manage the vehicles referenced by `lockVehicle()` and `unlockVehicle()`.

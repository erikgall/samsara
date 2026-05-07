---
title: Vehicles
nav_order: 2
description: Manage vehicles in your Samsara fleet.
permalink: /resources/vehicles
---

# Vehicles

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Updating Records](#updating-records)
- [Unsupported Operations](#unsupported-operations)
  - [Create](#create)
  - [Delete](#delete)
- [Filtering](#filtering)
- [External IDs](#external-ids)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

A vehicle is a motorized asset tracked by a Samsara vehicle gateway — the on-board device that emits GPS, engine state, fuel level, and the rest of the telemetry stream. Vehicles are distinct from generic assets (`Samsara::assets()`), trailers, and equipment. Use this resource to read the vehicle roster and update non-physical fields like name, notes, and license plate. Creating and deleting vehicles must go through the Assets API — see below.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$vehicles = Samsara::vehicles()->all();

$vehicle = Samsara::vehicles()->find('vehicle-id');
```

## Updating Records

```php
$vehicle = Samsara::vehicles()->update('vehicle-id', [
    'name' => 'Truck 001 — Updated',
]);
```

## Unsupported Operations

The Samsara API does not support creating or deleting vehicles via `/fleet/vehicles`. Calling these methods raises an `UnsupportedOperationException`.

### Create

Vehicles are auto-created when a Samsara Vehicle Gateway is installed. To create a vehicle record manually, use the Assets API.

```php
use Samsara\Exceptions\UnsupportedOperationException;

try {
    $vehicle = Samsara::vehicles()->create([
        'name' => 'Truck 001',
        'vin' => '1HGBH41JXMN109186',
    ]);
} catch (UnsupportedOperationException $e) {
    // Vehicles cannot be created via /fleet/vehicles.
}

$vehicle = Samsara::assets()->create([
    'type' => 'vehicle',
    'name' => 'Truck 001',
]);
```

### Delete

Vehicles cannot be deleted. To retire one, update its name or notes field.

```php
use Samsara\Exceptions\UnsupportedOperationException;

try {
    Samsara::vehicles()->delete('vehicle-id');
} catch (UnsupportedOperationException $e) {
    // Vehicles cannot be deleted via the Samsara API.
}

Samsara::vehicles()->update('vehicle-id', [
    'name' => '[RETIRED] Truck 001',
]);
```

## Filtering

See [Query Builder](../query-builder.md) for the full filter list.

```php
$vehicles = Samsara::vehicles()
    ->query()
    ->whereTag('tag-id')
    ->get();

$vehicles = Samsara::vehicles()
    ->query()
    ->whereTag(['tag-1', 'tag-2'])
    ->get();

$vehicles = Samsara::vehicles()
    ->query()
    ->limit(50)
    ->paginate();
```

## External IDs

```php
$vehicle = Samsara::vehicles()->findByExternalId('fleet_id', 'TRUCK001');
```

## Helper Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `getDisplayName()` | `string` | Vehicle name with a fallback when null. |
| `getExternalId(string $key)` | `?string` | Look up an external id by namespace key. |
| `getTagIds()` | `array<int, string>` | Flat list of tag ids associated with the vehicle. |
| `hasStaticAssignedDriver()` | `bool` | True when a driver is statically assigned. |
| `getStaticAssignedDriver()` | `?StaticAssignedDriver` | The statically assigned driver, when present. |

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `string` | Vehicle id. |
| `name` | `?string` | Vehicle name. |
| `vin` | `?string` | Vehicle identification number. |
| `make` | `?string` | Make. |
| `model` | `?string` | Model. |
| `year` | `?string` | Year. |
| `licensePlate` | `?string` | License plate. |
| `serial` | `?string` | Gateway serial number. |
| `notes` | `?string` | Free-form notes. |
| `vehicleType` | `?string` | Vehicle type. |
| `externalIds` | `?array<string, string>` | External id mappings keyed by namespace. |
| `tags` | `?array` | Associated tags. |
| `staticAssignedDriver` | `?array` | Statically assigned driver, when set. |

## Related Resources

- [Vehicle Stats](vehicle-stats.md) — telemetry data emitted by the gateway.
- [Vehicle Locations](vehicle-locations.md) — GPS positions, current and historical.
- [Trips](trips.md) — engine-on/engine-off intervals.
- [Assets](assets.md) — create or delete vehicle records here.

---
title: Assets
nav_order: 19
description: Manage non-vehicle fleet assets and their location, depreciation, and input streams.
permalink: /resources/assets
---

# Assets

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Specialized Streams](#specialized-streams)
- [Legacy Endpoints](#legacy-endpoints)
- [Asset Types](#asset-types)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The Assets resource covers the non-vehicle, non-trailer side of your fleet — generators, reefers, containers, and other tracked equipment. Reach for it when you need a single inventory across asset types, or when you need to read depreciation and input streams that are not exposed on the per-class resources. For typed access to vehicles, trailers, or fleet equipment, use [Vehicles](vehicles.md), [Trailers](trailers.md), or [Equipment](equipment.md) instead.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$assets = Samsara::assets()->all();

$asset = Samsara::assets()->find('asset-id');
```

## Creating Records

```php
$asset = Samsara::assets()->create([
    'name'      => 'Forklift A',
    'assetType' => 'equipment',
    'serial'    => 'FL-12345',
]);
```

The `assetType` field accepts any value from the [`AssetType` enum](#asset-types).

## Updating Records

```php
$asset = Samsara::assets()->update('asset-id', [
    'name' => 'Forklift A - Warehouse 1',
]);
```

## Deleting Records

```php
Samsara::assets()->delete('asset-id');
```

## Filtering

Assets accept the standard query builder. See [the query builder reference](../query-builder.md) for the full method list.

```php
$assets = Samsara::assets()
    ->query()
    ->whereTag('warehouse-equipment')
    ->limit(50)
    ->get();
```

## Specialized Streams

Three sub-builders expose stream endpoints for asset telemetry. Each returns a `Builder` you can compose with the standard filters.

```php
use Carbon\Carbon;

$depreciation = Samsara::assets()
    ->depreciation()
    ->get();

$inputs = Samsara::assets()
    ->inputsStream()
    ->get();

$locations = Samsara::assets()
    ->locationAndSpeedStream()
    ->between(Carbon::now()->subHours(24), Carbon::now())
    ->get();
```

## Legacy Endpoints

Two legacy v1 endpoints remain available for parity with older integrations. They return the raw v1 response body as an array — they do not produce typed `Asset` entities.

> **Note:** Prefer the v2 stream endpoints above for new integrations. The legacy endpoints are kept for backwards compatibility only.

```php
$locations = Samsara::assets()->locations([
    'groupId' => 'group-123',
]);

$reefers = Samsara::assets()->reefers([
    'groupId' => 'group-123',
]);
```

## Asset Types

The `Samsara\Enums\AssetType` enum lists the asset types Samsara recognizes. Pass either the enum value or its raw string anywhere `assetType` is accepted.

| Case | Value | Description |
|------|-------|-------------|
| `CONTAINER` | `container` | Shipping container. |
| `EQUIPMENT` | `equipment` | Generic equipment asset. |
| `GENERATOR` | `generator` | Generator. |
| `OTHER` | `other` | Asset that does not fit another category. |
| `REEFER` | `reefer` | Refrigerated unit. |
| `TRAILER` | `trailer` | Trailer. |
| `UNPOWERED` | `unpowered` | Unpowered asset (e.g., dollies, chassis). |

See the [enum reference](../enums.md) for the canonical value mapping across the SDK.

## Helper Methods

The `Asset` entity exposes type-check helpers:

```php
$asset = Samsara::assets()->find('asset-id');

$asset->isVehicle();   // bool
$asset->isTrailer();   // bool
$asset->isEquipment(); // bool
```

## Properties

The `Asset` entity (`Samsara\Data\Asset\Asset`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Asset ID. |
| `name` | `?string` | Asset name. |
| `serial` | `?string` | Serial number. |
| `assetType` | `?string` | Asset type — see [`AssetType`](#asset-types). |
| `purchasePrice` | `?float` | Purchase price. |
| `gateway` | `?array` | Installed gateway info — `{id, serial}`. |
| `location` | `?array` | Current location — `{latitude, longitude}`. |
| `tags` | `?array` | Associated tags. Each entry is `{id, name?}`. |
| `createdAtTime` | `?string` | Creation timestamp (RFC 3339). |
| `updatedAtTime` | `?string` | Last update timestamp (RFC 3339). |

## Related Resources

- [Vehicles](vehicles.md) — vehicle-specific resource with richer typed data.
- [Trailers](trailers.md) — trailer-specific resource.
- [Equipment](equipment.md) — equipment-specific resource.
- [Tags](tags.md) — group assets for filtering.
- [Query Builder](../query-builder.md) — for filtering, pagination, and lazy iteration.

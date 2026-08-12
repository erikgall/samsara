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

The Assets resource covers the non-vehicle, non-trailer side of your fleet — generators, reefers, containers, and other tracked equipment. Reach for it when you need a single inventory across asset types, or when you need to read depreciation and input streams that are not exposed on the per-class resources. For typed access to vehicles, trailers, or fleet equipment, use [Vehicles](vehicles.html), [Trailers](trailers.html), or [Equipment](equipment.html) instead.

`all()`, `query()`, and `create()` use `/assets`. The resource overrides `update()` and `delete()` to use `/assets?id={id}`. `find()` is unsupported and throws before HTTP.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$assets = Samsara::assets()->all();
```

## Creating Records

```php
$asset = Samsara::assets()->create([
    'name'   => 'Forklift A',
    'type'   => 'equipment',
    'serialNumber' => 'FL-12345',
]);
```

Request and response DTOs use `type` and `serialNumber`.

## Updating Records

```php
$asset = Samsara::assets()->update('asset-id', [
    'name' => 'Forklift A - Warehouse 1',
]);
```

The request is `PATCH /assets?id=asset-id`.

## Deleting Records

```php
Samsara::assets()->delete('asset-id');
```

The request is `DELETE /assets?id=asset-id`.

## Filtering

The list operation accepts asset type/ID, tag, attribute, external-ID, update-time, include, and cursor filters. See [the query builder reference](../query-builder.html) for the fluent method mapping.

```php
$assets = Samsara::assets()
    ->query()
    ->whereTag('warehouse-equipment')
    ->get();
```

## Specialized Streams

Three sub-builders expose stream endpoints for asset telemetry. Each returns a `Builder`, but each endpoint has its own filter requirements.

```php
use Carbon\Carbon;

$depreciation = Samsara::assets()
    ->depreciation()
    ->get();

$inputs = Samsara::assets()
    ->inputsStream()
    ->where('ids', 'asset-id')
    ->where('type', 'auxInput1')
    ->startTime(Carbon::now()->subHours(24))
    ->get();

$locations = Samsara::assets()
    ->locationAndSpeedStream()
    ->between(Carbon::now()->subHours(24), Carbon::now())
    ->get();
```

`depreciation()` uses `/assets/depreciation` and maps generic `Entity`; `inputsStream()` uses `/assets/inputs/stream` and maps generic `Entity`; `locationAndSpeedStream()` maps `AssetLocation`. The first two no-limit cursor builders suppress the implicit page size.

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

The `Samsara\Enums\AssetType` enum is the package's current value set; it is not the current upstream `/assets` request enum.

| Case | Value | Description |
|------|-------|-------------|
| `CONTAINER` | `container` | Shipping container. |
| `EQUIPMENT` | `equipment` | Generic equipment asset. |
| `GENERATOR` | `generator` | Generator. |
| `OTHER` | `other` | Asset that does not fit another category. |
| `REEFER` | `reefer` | Refrigerated unit. |
| `TRAILER` | `trailer` | Trailer. |
| `UNPOWERED` | `unpowered` | Unpowered asset (e.g., dollies, chassis). |

Current upstream `type` values are `uncategorized`, `trailer`, `equipment`, `unpowered`, and `vehicle`. Only three overlap with the package enum; the package omits `uncategorized` and `vehicle`, and adds four values that the current schema does not accept. See [Enums](../enums.html) for the package mapping.

## Helper Methods

The `Asset` entity exposes type-check helpers:

```php
$asset = Samsara::assets()->all()->first();

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
| `serialNumber` | `?string` | Serial number. |
| `type` | `?string` | Asset type — see [`AssetType`](#asset-types). |
| `attributes` | `?array` | Custom attributes returned for the asset. |
| `externalIds` | `?array<string, string>` | External identifier map. |
| `licensePlate` | `?string` | License plate. |
| `make` | `?string` | Manufacturer. |
| `model` | `?string` | Model. |
| `notes` | `?string` | Asset notes. |
| `readingsIngestionEnabled` | `?bool` | Whether readings ingestion is enabled. |
| `regulationMode` | `?string` | Regulation mode. |
| `tags` | `?array` | Associated tags. Each entry is `{id, name?}`. |
| `vin` | `?string` | Vehicle identification number, when present. |
| `year` | `?int` | Model year. |
| `createdAtTime` | `?string` | Creation timestamp (RFC 3339). |
| `updatedAtTime` | `?string` | Last update timestamp (RFC 3339). |

## Related Resources

- [Vehicles](vehicles.html) — vehicle-specific resource with richer typed data.
- [Trailers](trailers.html) — trailer-specific resource.
- [Equipment](equipment.html) — equipment-specific resource.
- [Tags](tags.html) — group assets for filtering.
- [Query Builder](../query-builder.html) — for filtering, pagination, and lazy iteration.

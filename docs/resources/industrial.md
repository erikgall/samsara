---
title: Industrial
nav_order: 20
description: Industrial assets and machine telemetry for non-vehicle equipment.
permalink: /resources/industrial
---

# Industrial

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Data Inputs and Data Points](#data-inputs-and-data-points)
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::industrial()` resource manages industrial assets — non-vehicle equipment such as CNC machines, generators, and tank farms — alongside the data inputs and data points that those assets emit. Use this resource (not [Assets](assets.md)) when you monitor machinery and stationary equipment connected through Samsara's industrial gateways.

## Retrieving Records

The default builder targets `/industrial/assets` and returns typed `IndustrialAsset` entities.

```php
use Samsara\Facades\Samsara;

$asset = Samsara::industrial()->find('asset-id');

$assets = Samsara::industrial()->all();

$active = Samsara::industrial()
    ->assets()
    ->whereTag('production-floor')
    ->get();
```

`assets()` and `query()` both return a fresh `Builder` rooted at `/industrial/assets`; use whichever reads better in context.

## Creating Records

```php
$asset = Samsara::industrial()->createAsset([
    'name' => 'CNC Machine 1',
    'location' => [
        'latitude' => 37.7749,
        'longitude' => -122.4194,
    ],
]);
```

`createAsset()` returns the saved `IndustrialAsset` directly.

## Updating Records

```php
$asset = Samsara::industrial()->updateAsset('asset-id', [
    'name' => 'CNC Machine 1 — Bay A',
]);
```

## Deleting Records

```php
Samsara::industrial()->deleteAsset('asset-id');
```

`deleteAsset()` returns `true` on a successful response and raises a typed exception otherwise (see [Error Handling](../error-handling.md)).

## Data Inputs and Data Points

`IndustrialResource` exposes additional builders for the telemetry that industrial assets emit. Each builder is a generic `Builder` — its results are unwrapped into generic `Entity` instances, since only the assets endpoint is typed.

```php
$inputs = Samsara::industrial()
    ->dataInputs()
    ->whereTag('production-floor')
    ->get();

$points = Samsara::industrial()
    ->dataPoints()
    ->get();

$feed = Samsara::industrial()
    ->dataPointsFeed()
    ->get();

$history = Samsara::industrial()
    ->dataPointsHistory()
    ->between(now()->subDays(7), now())
    ->get();
```

| Builder | Endpoint | Use it for |
|---------|----------|------------|
| `dataInputs()` | `/industrial/data-inputs` | Discover data inputs configured on an asset. |
| `dataPoints()` | `/industrial/data-points` | Latest readings per data input. |
| `dataPointsFeed()` | `/industrial/data-points/feed` | Cursor feed of newly recorded readings. |
| `dataPointsHistory()` | `/industrial/data-points/history` | Reading history over an explicit `between()` window. |

## Filtering

Refer to the [query builder](../query-builder.md) for the full operator reference. The asset builder accepts the standard filters:

```php
$assets = Samsara::industrial()
    ->query()
    ->whereTag('production-floor')
    ->limit(25)
    ->get();
```

## Properties

`IndustrialAsset` exposes the following `@property-read` keys:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Industrial asset ID. |
| `name` | `?string` | Asset name. |
| `dataInputs` | `?array<int, array{id?: string, name?: string}>` | Data inputs associated with the asset. |
| `location` | `?array{latitude?: float, longitude?: float}` | Asset location. |
| `tags` | `?array<int, array{id?: string, name?: string}>` | Tags assigned to the asset. |

The data-input and data-point builders return generic `Entity` instances rather than typed classes. Inspect their payloads via `$entity->toArray()` or array access.

## Related Resources

- [Assets](assets.md) — non-powered tracker assets (vs. industrial machines).
- [Sensors](sensors.md) — legacy v1 sensor endpoints for older industrial gateways.
- [Tags](tags.md) — group industrial assets for filtering.
- [Query Builder](../query-builder.md) — operators available on every builder.

---
title: Trips
nav_order: 7
description: Access trip data for vehicles and drivers.
permalink: /resources/trips
---

# Trips

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Trip Status Filters](#trip-status-filters)
- [Time-Based Queries](#time-based-queries)
- [Streaming Large Datasets](#streaming-large-datasets)
- [Filtering](#filtering)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

A Samsara trip is an asset movement interval collected by the Trips stream, distinct from a planned dispatch route. Current rows carry the associated asset, completion state, start/end timestamps, locations, and record timestamps. The resource is read-only; trips are created and closed by Samsara, not by this API.

## Retrieving Records

```php
use Samsara\Facades\Samsara;
use Carbon\Carbon;

$trips = Samsara::trips()
    ->query()
    ->startTime(Carbon::now()->subDays(7))
    ->where('ids', 'vehicle-1,vehicle-2')
    ->get();
```

## Trip Status Filters

The resource ships two typed shortcuts that pre-filter on `completionStatus`.

```php
$completed = Samsara::trips()
    ->completed()
    ->startTime(now()->subDays(7))
    ->where('ids', 'vehicle-id')
    ->get();

$inProgress = Samsara::trips()
    ->inProgress()
    ->startTime(now()->subDay())
    ->where('ids', 'vehicle-id')
    ->get();
```

## Time-Based Queries

The official stream requires both `startTime` and comma-separated asset IDs in `ids`. The operation does not accept `limit`, so the builder suppresses the implicit configured page size.

```php
use Carbon\Carbon;

$today = Samsara::trips()
    ->query()
    ->startTime(Carbon::today())
    ->where('ids', 'vehicle-id')
    ->get();

$range = Samsara::trips()
    ->query()
    ->startTime('2026-01-01T00:00:00Z')
    ->where('ids', 'vehicle-1,vehicle-2')
    ->get();
```

## Streaming Large Datasets

For multi-month windows, use `lazy()` to iterate without loading every page into memory at once.

```php
Samsara::trips()
    ->query()
    ->startTime(now()->subYear())
    ->where('ids', 'vehicle-id')
    ->lazy()
    ->each(function ($trip) {
        DB::table('trip_archive')->insert([
            'samsara_trip_start' => $trip->tripStartTime,
            'samsara_trip_end' => $trip->tripEndTime,
            'asset_id' => $trip->asset['id'] ?? null,
            'completion_status' => $trip->completionStatus,
        ]);
    });
```

## Filtering

See [Query Builder](../query-builder.html) for the generic builder surface. The current Trips stream requires `startTime` and comma-separated asset `ids`; it also accepts `endTime`, `queryBy`, `completionStatus`, and `includeAsset`.

```php
$trips = Samsara::trips()
    ->query()
    ->startTime(now()->subDays(7))
    ->where('ids', 'vehicle-1,vehicle-2')
    ->where('includeAsset', true)
    ->where('queryBy', 'tripStartTime')
    ->get();
```

## Helper Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `isCompleted()` | `bool` | True only when `completionStatus === 'completed'`. |
| `isInProgress()` | `bool` | True only when `completionStatus === 'inProgress'`. |

```php
$trip = Samsara::trips()
    ->query()
    ->startTime(now()->subDay())
    ->where('ids', 'vehicle-id')
    ->get()
    ->first();

$trip->isCompleted();           // bool
$trip->isInProgress();          // bool
$trip->asset['id'] ?? null;     // ?string
```

## Properties

The current Trip response exposes one nested `asset`; it does not expose the legacy distance, driving-duration, driver, or vehicle fields.

| Property | Type | Description |
|----------|------|-------------|
| `tripStartTime` | `?string` | RFC 3339 trip start time. |
| `tripEndTime` | `?string` | RFC 3339 trip end time. |
| `createdAtTime` | `?string` | Record creation time (RFC 3339). |
| `updatedAtTime` | `?string` | Record update time (RFC 3339). |
| `completionStatus` | `?string` | `inProgress` or `completed`. |
| `asset` | `?array{id?: string, name?: string}` | Associated asset. |
| `startLocation` | `?array{latitude?: float, longitude?: float}` | Trip start location. |
| `endLocation` | `?array{latitude?: float, longitude?: float}` | Trip end location. |

## Related Resources

- [Vehicles](vehicles.html) — vehicles whose gateways produce trips.
- [Assets](assets.html) — asset records referenced by Trip rows.
- [Vehicle Stats](vehicle-stats.html) — engine-state and GPS samples that compose a trip.

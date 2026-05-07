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

A Samsara trip is the engine-on to engine-off interval recorded by a vehicle gateway — distinct from a planned dispatch route. Trips carry the driver and vehicle that produced them, the start/end timestamps and locations, and the distance and driving time. The resource is read-only; trips are created and closed by the gateway, not by the API.

## Retrieving Records

```php
use Samsara\Facades\Samsara;
use Carbon\Carbon;

$trips = Samsara::trips()
    ->query()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();
```

## Trip Status Filters

The resource ships two typed shortcuts that pre-filter on `completionStatus`.

```php
$completed = Samsara::trips()
    ->completed()
    ->between(now()->subDays(7), now())
    ->get();

$inProgress = Samsara::trips()
    ->inProgress()
    ->get();
```

## Time-Based Queries

`between()` covers the common case. For explicit RFC 3339 strings use `startTime()` and `endTime()`.

```php
use Carbon\Carbon;

$today = Samsara::trips()
    ->query()
    ->between(Carbon::today(), Carbon::now())
    ->get();

$range = Samsara::trips()
    ->query()
    ->startTime('2024-01-01T00:00:00Z')
    ->endTime('2024-01-31T23:59:59Z')
    ->get();
```

## Streaming Large Datasets

For multi-month windows, use `lazy()` to iterate without loading every page into memory at once.

```php
Samsara::trips()
    ->query()
    ->between(now()->subYear(), now())
    ->lazy(500)
    ->each(function ($trip) {
        DB::table('trip_archive')->insert([
            'samsara_trip_start' => $trip->startTime,
            'driver_id' => $trip->driver['id'] ?? null,
            'vehicle_id' => $trip->vehicle['id'] ?? null,
            'distance_meters' => $trip->distanceMeters,
        ]);
    });
```

## Filtering

See [Query Builder](../query-builder.md) for the complete filter list. The most common Trip filters are by driver, vehicle, and tag.

```php
$trips = Samsara::trips()
    ->query()
    ->whereDriver('driver-id')
    ->between(now()->subWeek(), now())
    ->get();

$trips = Samsara::trips()
    ->query()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->between(now()->subDays(7), now())
    ->get();

$trips = Samsara::trips()
    ->query()
    ->whereTag('delivery-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```

## Helper Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `getDistanceMiles()` | `?float` | Distance converted from `distanceMeters` to miles. |
| `getDistanceKilometers()` | `?float` | Distance converted from `distanceMeters` to kilometers. |
| `getDrivingTimeHours()` | `?float` | Driving time converted from `drivingTimeMs` to hours. |
| `getDrivingTimeMinutes()` | `?float` | Driving time converted from `drivingTimeMs` to minutes. |

```php
$trip = Samsara::trips()
    ->query()
    ->between(now()->subDay(), now())
    ->first();

$trip->getDistanceMiles();      // ?float
$trip->getDrivingTimeHours();   // ?float
$trip->driver['id'] ?? null;    // ?string
$trip->vehicle['id'] ?? null;   // ?string
```

## Properties

The Trip entity exposes nested `driver` and `vehicle` arrays, not flat `driverId` / `vehicleId` keys.

| Property | Type | Description |
|----------|------|-------------|
| `startTime` | `?string` | RFC 3339 trip start time. |
| `endTime` | `?string` | RFC 3339 trip end time. |
| `distanceMeters` | `?int` | Trip distance in meters. |
| `drivingTimeMs` | `?int` | Driving time in milliseconds. |
| `driver` | `?array{id?: string, name?: string}` | Associated driver. |
| `vehicle` | `?array{id?: string, name?: string}` | Associated vehicle. |
| `asset` | `?array{id?: string, name?: string}` | Associated asset (when the trip was made by a non-vehicle asset). |
| `startLocation` | `?array{latitude?: float, longitude?: float}` | Trip start location. |
| `endLocation` | `?array{latitude?: float, longitude?: float}` | Trip end location. |

## Related Resources

- [Vehicles](vehicles.md) — vehicles whose gateways produce trips.
- [Drivers](drivers.md) — drivers attributed to each trip.
- [Vehicle Stats](vehicle-stats.md) — engine-state and GPS samples that compose a trip.

---
title: Vehicle Locations
nav_order: 6
description: Access real-time and historical vehicle location data.
permalink: /resources/vehicle-locations
---

# Vehicle Locations

- [Introduction](#introduction)
- [Current Locations](#current-locations)
- [Location Feed](#location-feed)
- [Location History](#location-history)
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Vehicle locations expose the GPS coordinates each vehicle gateway emits — the latest known position, a polling feed for low-latency updates, and a historical track for arbitrary time windows. Pick `current()` for an at-a-glance map, `feed()` for incremental polling, and `history()` for back-fill or audit work.

The current snapshot and the feed/history series use different DTOs. `current()` and `query()` produce `VehicleLocationResult`; `feed()` and `history()` produce `VehicleLocationSeries`. All builders omit the configured implicit page size because these cursor endpoints have no official `limit`.

## Current Locations

`current()` returns the latest known location for each vehicle.

`query()` is an alias for a new builder rooted at the same current-locations endpoint.

```php
use Samsara\Facades\Samsara;

$locations = Samsara::vehicleLocations()->current()->get();

$locations = Samsara::vehicleLocations()
    ->current()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->get();

$locations = Samsara::vehicleLocations()
    ->current()
    ->whereTag('fleet-west')
    ->get();
```

## Location Feed

The feed endpoint is the polling primitive: each call returns the records produced since the cursor returned by the previous call.

```php
$feed = Samsara::vehicleLocations()
    ->feed()
    ->get();

$feed = Samsara::vehicleLocations()
    ->feed()
    ->whereVehicle('vehicle-id')
    ->get();
```

## Location History

`history()` accepts a time window. `get()` returns one response page; use `lazy()` to walk every cursor page in a long window.

```php
use Carbon\Carbon;

$history = Samsara::vehicleLocations()
    ->history()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();

$history = Samsara::vehicleLocations()
    ->history()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->between(Carbon::now()->subHours(24), Carbon::now())
    ->get();

// Stream a long window without buffering every page
Samsara::vehicleLocations()
    ->history()
    ->between(Carbon::now()->subMonth(), Carbon::now())
    ->lazy()
    ->each(function ($series) {
        foreach ($series->getLocations() as $location) {
            // Process each typed VehicleLocation sample.
        }
    });
```

## Filtering

See [Query Builder](../query-builder.html) for the full filter list. The most common filters are by vehicle, tag, and parent tag — all chainable on any of the three shortcuts above.

```php
$locations = Samsara::vehicleLocations()
    ->current()
    ->whereTag('active-fleet')
    ->get();

$locations = Samsara::vehicleLocations()
    ->current()
    ->whereParentTag('region-west')
    ->get();

$locations = Samsara::vehicleLocations()
    ->current()
    ->whereTag('delivery-trucks')
    ->get();
```

## Properties

The snapshot response is `Samsara\Data\Vehicle\VehicleLocationResult` with the following keys.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `string` | Vehicle id (the location is keyed by vehicle, not by sample). |
| `name` | `?string` | Vehicle name at the time of the sample. |
| `location` | `?array` | The latest location payload — `latitude`, `longitude`, `heading`, `speed`, `time`, optional `reverseGeo`. |

Call `$result->getLocation()` to obtain a typed `VehicleLocation`. Feed/history results expose `id`, `name`, and a `locations` array; `$series->getLocations()` returns `EntityCollection<int, VehicleLocation>`.

## Related Resources

- [Vehicles](vehicles.html) — the vehicles whose gateways emit these samples.
- [Vehicle Stats](vehicle-stats.html) — pair location with telemetry (engine state, fuel) on the same vehicle.
- [Trips](trips.html) — engine-on/engine-off trip envelopes built from location and stats data.

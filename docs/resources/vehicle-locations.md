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
- [Properties](#available-properties)
- [Related Resources](#related-resources)

## Introduction

Vehicle locations expose the GPS coordinates each vehicle gateway emits — the latest known position, a polling feed for low-latency updates, and a historical track for arbitrary time windows. Pick `current()` for an at-a-glance map, `feed()` for incremental polling, and `history()` for back-fill or audit work.

> **Note:** `VehicleLocationsResource` does not declare a typed `$entity`. Each result is a generic `Entity` (a `Fluent` instance) keyed by the response shape documented in [Properties](#available-properties). There are no `getLatitude()` / `getLongitude()` helpers on the result; access keys directly with `$location->latitude`.

## Current Locations

`current()` returns the latest known location for each vehicle.

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

`history()` accepts a time window and returns every sample for it.

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
    ->lazy(500)
    ->each(function ($location) {
        // Process each sample
    });
```

## Filtering

See [Query Builder](../query-builder.md) for the full filter list. The most common filters are by vehicle, tag, and parent tag — all chainable on any of the three shortcuts above.

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
    ->limit(50)
    ->get();
```

## Properties

The response is a generic `Entity` with the following keys.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `string` | Vehicle id (the location is keyed by vehicle, not by sample). |
| `name` | `?string` | Vehicle name at the time of the sample. |
| `location` | `?array` | The latest location payload — `latitude`, `longitude`, `heading`, `speed`, `time`, optional `reverseGeo`. |

> **Note:** Sub-keys inside `location` (such as `latitude` or `time`) come straight from the Samsara response. They are accessible as array keys, not as typed properties.

## Related Resources

- [Vehicles](vehicles.md) — the vehicles whose gateways emit these samples.
- [Vehicle Stats](vehicle-stats.md) — pair location with telemetry (engine state, fuel) on the same vehicle.
- [Trips](trips.md) — engine-on/engine-off trip envelopes built from location and stats data.

---
title: Trips
layout: default
parent: Resources
nav_order: 7
description: "Access trip data for vehicles and drivers"
permalink: /resources/trips
---

# Trips Resource

Access trip data for vehicles and drivers.

## Basic Usage

```php
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get trips stream
$trips = Samsara::trips()
    ->query()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();
```

## Query Builder

```php
// Filter by driver
$trips = Samsara::trips()
    ->query()
    ->whereDriver('driver-123')
    ->between(now()->subWeek(), now())
    ->get();

// Filter by vehicle
$trips = Samsara::trips()
    ->query()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->between(now()->subDays(7), now())
    ->get();

// Filter by tag
$trips = Samsara::trips()
    ->query()
    ->whereTag('delivery-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```

## Trip Status Filters

```php
// Get completed trips only
$completedTrips = Samsara::trips()
    ->completed()
    ->between(now()->subDays(7), now())
    ->get();

// Get in-progress trips only
$inProgressTrips = Samsara::trips()
    ->inProgress()
    ->get();
```

## Time-Based Queries

```php
use Carbon\Carbon;

// Get trips for today
$todayTrips = Samsara::trips()
    ->query()
    ->between(Carbon::today(), Carbon::now())
    ->get();

// Get trips for a specific date range
$trips = Samsara::trips()
    ->query()
    ->startTime('2024-01-01T00:00:00Z')
    ->endTime('2024-01-31T23:59:59Z')
    ->get();
```

## Lazy Loading Large Datasets

```php
// Stream through large trip datasets
Samsara::trips()
    ->query()
    ->between(now()->subYear(), now())
    ->lazy(500)
    ->each(function ($trip) {
        // Process each trip
        DB::table('trip_archive')->insert([
            'samsara_id' => $trip->getId(),
            'driver_id' => $trip->driverId,
            'distance' => $trip->distanceMeters,
        ]);
    });
```

## Trip Entity

```php
$trip = Samsara::trips()->query()->first();

$trip->id;               // string
$trip->driverId;         // ?string
$trip->vehicleId;        // ?string
$trip->startTime;        // string (ISO 8601)
$trip->endTime;          // ?string (ISO 8601)
$trip->distanceMeters;   // ?float
$trip->completionStatus; // string ('completed' or 'inProgress')
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Trip ID |
| `driverId` | string | Driver ID |
| `vehicleId` | string | Vehicle ID |
| `startTime` | string | Trip start time (ISO 8601) |
| `endTime` | string | Trip end time (ISO 8601) |
| `distanceMeters` | float | Trip distance in meters |
| `completionStatus` | string | 'completed' or 'inProgress' |
| `startLocation` | object | Start location details |
| `endLocation` | object | End location details |

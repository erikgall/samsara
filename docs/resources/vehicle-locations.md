---
title: Vehicle Locations
layout: default
parent: Resources
nav_order: 6
description: "Access real-time and historical vehicle location data"
permalink: /resources/vehicle-locations
---

# Vehicle Locations Resource

Access real-time and historical vehicle location data.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get current locations for all vehicles
$locations = Samsara::vehicleLocations()->all();
```

## Current Locations

```php
// Get current vehicle locations
$locations = Samsara::vehicleLocations()->current()->get();

// Filter by specific vehicles
$locations = Samsara::vehicleLocations()
    ->current()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->get();

// Filter by tag
$locations = Samsara::vehicleLocations()
    ->current()
    ->whereTag('fleet-west')
    ->get();
```

## Location Feed

Use the feed endpoint to poll for location updates:

```php
// Get location feed (for polling new data)
$feed = Samsara::vehicleLocations()
    ->feed()
    ->get();

// Filter feed by vehicles
$feed = Samsara::vehicleLocations()
    ->feed()
    ->whereVehicle('vehicle-123')
    ->get();
```

## Location History

```php
use Carbon\Carbon;

// Get location history for a time range
$history = Samsara::vehicleLocations()
    ->history()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();

// Get history for specific vehicles
$history = Samsara::vehicleLocations()
    ->history()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->between(Carbon::now()->subHours(24), Carbon::now())
    ->get();

// Lazy load large history datasets
$history = Samsara::vehicleLocations()
    ->history()
    ->between(Carbon::now()->subMonth(), Carbon::now())
    ->lazy(500)
    ->each(function ($location) {
        // Process each location
    });
```

## Query Filters

```php
// Filter by tag
$locations = Samsara::vehicleLocations()
    ->query()
    ->whereTag('active-fleet')
    ->get();

// Filter by parent tag
$locations = Samsara::vehicleLocations()
    ->query()
    ->whereParentTag('region-west')
    ->get();

// Combine filters
$locations = Samsara::vehicleLocations()
    ->current()
    ->whereTag('delivery-trucks')
    ->limit(50)
    ->get();
```

## Location Data Structure

Each location record includes:

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Location record ID |
| `vehicleId` | string | Vehicle ID |
| `latitude` | float | Latitude coordinate |
| `longitude` | float | Longitude coordinate |
| `heading` | float | Heading in degrees |
| `speed` | float | Speed in meters per second |
| `time` | string | ISO 8601 timestamp |
| `reverseGeo` | object | Reverse geocoded address |

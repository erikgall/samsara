---
title: Vehicle Stats
layout: default
parent: Resources
nav_order: 5
description: "Retrieve telemetry data for vehicles"
permalink: /resources/vehicle-stats
---

# Vehicle Stats Resource

Retrieve telemetry data for vehicles.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get current stats
$stats = Samsara::vehicleStats()
    ->types(['gps', 'engineStates'])
    ->get();
```

## Stat Types

Use the `VehicleStatType` enum or strings:

```php
use Samsara\Enums\VehicleStatType;

// Using enum
$stats = Samsara::vehicleStats()
    ->types([
        VehicleStatType::GPS,
        VehicleStatType::ENGINE_STATES,
        VehicleStatType::FUEL_PERCENTS,
    ])
    ->get();

// Using strings
$stats = Samsara::vehicleStats()
    ->types(['gps', 'engineStates', 'fuelPercents'])
    ->get();
```

### Available Stat Types

| Type | Description |
|------|-------------|
| `gps` | GPS location |
| `engineStates` | Engine on/off/idle |
| `fuelPercents` | Fuel level |
| `obdOdometerMeters` | OBD odometer |
| `gpsOdometerMeters` | GPS odometer |
| `engineRpm` | Engine RPM |
| `batteryMilliVolts` | Battery voltage |
| `engineCoolantTemperatureMilliC` | Coolant temp |
| `defLevelMilliPercent` | DEF level |
| `obdEngineSeconds` | Engine hours |

## Query Methods

### Current Stats

Get the most recent stats:

```php
$stats = Samsara::vehicleStats()
    ->types(['gps'])
    ->get();
```

### Stats Feed

Get a streaming feed of stats:

```php
$stats = Samsara::vehicleStats()
    ->feed()
    ->types(['gps', 'engineStates'])
    ->get();
```

### Historical Stats

Get stats for a time range:

```php
$stats = Samsara::vehicleStats()
    ->history()
    ->types(['gps'])
    ->between('2024-01-01', '2024-01-31')
    ->get();
```

## Filtering

### By Vehicle

```php
$stats = Samsara::vehicleStats()
    ->types(['gps'])
    ->whereVehicle('vehicle-id')
    ->get();

// Multiple vehicles
$stats = Samsara::vehicleStats()
    ->types(['gps'])
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->get();
```

### By Tag

```php
$stats = Samsara::vehicleStats()
    ->types(['gps'])
    ->whereTag('tag-id')
    ->get();
```

### With Decorations

Include address or reverse geocoding:

```php
$stats = Samsara::vehicleStats()
    ->types(['gps'])
    ->withDecorations(['address', 'reverseGeo'])
    ->get();
```

## Time Ranges

```php
use Carbon\Carbon;

// Between dates
$stats = Samsara::vehicleStats()
    ->types(['gps'])
    ->between(Carbon::now()->subDay(), Carbon::now())
    ->get();

// Start and end time
$stats = Samsara::vehicleStats()
    ->types(['gps'])
    ->startTime('2024-01-01T00:00:00Z')
    ->endTime('2024-01-31T23:59:59Z')
    ->get();
```

## Lazy Loading

For large datasets, use lazy loading:

```php
Samsara::vehicleStats()
    ->types(['gps'])
    ->between($start, $end)
    ->lazy()
    ->each(function ($stat) {
        // Process each stat
    });
```

## VehicleStats Entity

```php
$stat = Samsara::vehicleStats()->types(['gps'])->first();

// Get GPS location
$gps = $stat->getGps(); // ?GpsLocation

// Get engine state
$state = $stat->getEngineState(); // ?EngineState enum

// Check engine status
$stat->isEngineOn();   // bool
$stat->isEngineOff();  // bool
$stat->isEngineIdle(); // bool

// Get values
$stat->getFuelPercent();           // ?int
$stat->getObdOdometerMeters();     // ?int
$stat->getGpsOdometerMeters();     // ?int
$stat->getEngineRpm();             // ?int
$stat->getBatteryMilliVolts();     // ?int
$stat->getEngineCoolantTemperatureMilliC(); // ?int
$stat->getDefLevelMilliPercent();  // ?int
$stat->getObdEngineSeconds();      // ?int
```

## GpsLocation Entity

```php
$gps = $stat->getGps();

$gps->latitude;          // float
$gps->longitude;         // float
$gps->time;              // string
$gps->headingDegrees;    // ?int
$gps->speedMilesPerHour; // ?float
```

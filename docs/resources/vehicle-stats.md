---
title: Vehicle Stats
nav_order: 5
description: Retrieve telemetry data for vehicles.
permalink: /resources/vehicle-stats
---

# Vehicle Stats

- [Introduction](#introduction)
- [Choosing An Endpoint](#choosing-an-endpoint)
  - [Current Stats](#current-stats)
  - [Stats Feed](#stats-feed)
  - [Historical Stats](#historical-stats)
- [Typed Shortcuts](#typed-shortcuts)
- [Selecting Stat Types](#selecting-stat-types)
- [Filtering](#filtering)
- [Time Ranges](#time-ranges)
- [Streaming Large Datasets](#streaming-large-datasets)
- [VehicleStats Entity](#vehiclestats-entity)
- [GpsLocation Entity](#gpslocation-entity)
- [Related Resources](#related-resources)

## Introduction

Vehicle stats expose the per-vehicle telemetry stream — GPS, engine state, fuel level, odometer, RPM, EV battery, spreader inputs, and so on. The Samsara API splits this stream into three endpoints (current, feed, history), and the SDK exposes each as a builder. **`Samsara::vehicleStats()` returns the resource, not a builder** — every example below first calls `current()`, `history()`, `feed()`, or one of the typed shortcuts to obtain the builder you can chain `types()`, `whereVehicle()`, `between()`, and `get()` against.

## Choosing An Endpoint

### Current Stats

`current()` returns the most recent value for each requested stat. Use it for dashboards and "what is this vehicle doing right now" displays.

```php
use Samsara\Facades\Samsara;

$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps', 'engineStates'])
    ->get();
```

### Stats Feed

`feed()` is the polling primitive: each response carries an opaque cursor, and successive calls return the samples produced since the previous cursor. Use it for low-latency near-real-time pipelines.

```php
$stats = Samsara::vehicleStats()
    ->feed()
    ->types(['gps', 'engineStates'])
    ->get();
```

### Historical Stats

`history()` returns every sample inside a time window. Use it for back-fill, reporting, and audit work.

```php
$stats = Samsara::vehicleStats()
    ->history()
    ->types(['gps'])
    ->between('2024-01-01', '2024-01-31')
    ->get();
```

## Typed Shortcuts

The resource ships shortcuts for the most common single-type requests so you do not need to hand-roll `types([...])`.

| Shortcut | Equivalent To | Returns |
|----------|---------------|---------|
| `gps()` | `query()->types(['gps'])` | A `Builder` pre-typed for GPS samples. |
| `engineStates()` | `query()->types(['engineStates'])` | A `Builder` pre-typed for engine state. |
| `fuelPercents()` | `query()->types(['fuelPercents'])` | A `Builder` pre-typed for fuel level. |
| `odometer()` | `query()->types(['obdOdometerMeters', 'gpsOdometerMeters'])` | A `Builder` that returns OBD and GPS odometer values. |
| `query()` | New `Builder` instance | Empty builder; you choose `types()`. |

```php
$gps = Samsara::vehicleStats()
    ->gps()
    ->whereVehicle('vehicle-id')
    ->get();

$engine = Samsara::vehicleStats()
    ->engineStates()
    ->between(now()->subDay(), now())
    ->get();

$odometer = Samsara::vehicleStats()
    ->odometer()
    ->whereVehicle('vehicle-id')
    ->first();
```

> **Note:** The typed shortcuts hand back a `Builder`. They do not target a different endpoint — by default they hit the same endpoint as `current()`. Chain `between()` if you need historical samples through a typed shortcut.

## Selecting Stat Types

`types()` accepts a single string or an array of strings matching the API's stat-type wire values.

```php
$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps', 'engineStates', 'fuelPercents'])
    ->get();
```

The `VehicleStatType` enum lists every supported stat type. Pass the enum's `value` (or the case via `->value`) — **do not pass enum cases directly to `types()`**, which expects an `array<string>|string`.

```php
use Samsara\Enums\VehicleStatType;

$stats = Samsara::vehicleStats()
    ->current()
    ->types([
        VehicleStatType::GPS->value,
        VehicleStatType::ENGINE_STATES->value,
        VehicleStatType::FUEL_PERCENTS->value,
    ])
    ->get();
```

The most commonly requested stat types:

| Type | Description |
|------|-------------|
| `gps` | GPS location samples. |
| `engineStates` | Engine on/off/idle. |
| `fuelPercents` | Fuel level as a percentage. |
| `obdOdometerMeters` | OBD odometer in meters. |
| `gpsOdometerMeters` | GPS odometer in meters. |
| `engineRpm` | Engine RPM. |
| `batteryMilliVolts` | Battery voltage in millivolts. |
| `engineCoolantTemperatureMilliC` | Coolant temperature in milli-Celsius. |
| `defLevelMilliPercent` | DEF level as milli-percent. |
| `obdEngineSeconds` | Cumulative engine seconds. |

> **Note:** `VehicleStatType` defines 50 cases covering EV battery telemetry, spreader inputs, and tire pressures. See [Enums](../enums.md#vehiclestattype) for the complete list.

## Filtering

See [Query Builder](../query-builder.md) for the full filter list. The most common filters on stats are by vehicle, by tag, and with response decorations. Each chains onto a builder returned by `current()` / `history()` / `feed()` (or one of the typed shortcuts).

```php
// By vehicle
$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps'])
    ->whereVehicle('vehicle-id')
    ->get();

$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps'])
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->get();

// By tag
$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps'])
    ->whereTag('tag-id')
    ->get();

// With decorations (address, reverse geocoding)
$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps'])
    ->withDecorations(['address', 'reverseGeo'])
    ->get();
```

## Time Ranges

Time-range filters apply to `history()` and `feed()`. `between()` is the convenience method; `startTime()` / `endTime()` accept explicit RFC 3339 strings.

```php
use Carbon\Carbon;

$stats = Samsara::vehicleStats()
    ->history()
    ->types(['gps'])
    ->between(Carbon::now()->subDay(), Carbon::now())
    ->get();

$stats = Samsara::vehicleStats()
    ->history()
    ->types(['gps'])
    ->startTime('2024-01-01T00:00:00Z')
    ->endTime('2024-01-31T23:59:59Z')
    ->get();
```

## Streaming Large Datasets

Use `lazy()` on `history()` to iterate without buffering every page.

```php
Samsara::vehicleStats()
    ->history()
    ->types(['gps'])
    ->between($start, $end)
    ->lazy()
    ->each(function ($stat) {
        // Process each sample
    });
```

## VehicleStats Entity

```php
$stat = Samsara::vehicleStats()
    ->current()
    ->types(['gps'])
    ->first();

$gps = $stat->getGps();           // ?GpsLocation

$state = $stat->getEngineState(); // ?EngineState (see Enums)
$stat->isEngineOn();
$stat->isEngineOff();
$stat->isEngineIdle();

$stat->getFuelPercent();          // ?int
$stat->getObdOdometerMeters();    // ?int
$stat->getGpsOdometerMeters();    // ?int
$stat->getEngineRpm();            // ?int
$stat->getBatteryMilliVolts();    // ?int
$stat->getEngineCoolantTemperatureMilliC();
$stat->getDefLevelMilliPercent();
$stat->getObdEngineSeconds();
```

The `EngineState` enum defines three cases — `OFF`, `ON`, `IDLE`. See [Enums](../enums.md#enginestate) for the full reference.

## GpsLocation Entity

```php
$gps = $stat->getGps();

$gps->latitude;          // float
$gps->longitude;         // float
$gps->time;              // string (RFC 3339)
$gps->headingDegrees;    // ?int
$gps->speedMilesPerHour; // ?float
```

## Related Resources

- [Vehicles](vehicles.md) — the vehicles whose gateways emit these samples.
- [Vehicle Locations](vehicle-locations.md) — the GPS-only equivalent without the rest of the telemetry stream.
- [Trips](trips.md) — engine-on/engine-off envelopes built from these samples.
- [Enums](../enums.md#vehiclestattype) — the canonical `VehicleStatType` and `EngineState` references.

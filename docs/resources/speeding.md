---
title: Speeding
nav_order: 28
description: Speeding interval stream for compliance and safety monitoring.
permalink: /resources/speeding
---

# Speeding

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::speeding()` resource exposes the `/speeding-intervals/stream` feed of intervals during which a vehicle exceeded the posted limit. It is read-only and returns generic `Entity` instances, since `SpeedingResource` does not declare a typed `$entity`. Pair it with [Safety Events](safety-events.md) when you build coaching dashboards or monthly safety reports.

## Retrieving Records

Speeding intervals are streamed through the `intervalsStream()` builder. The base CRUD methods are inherited but the upstream endpoint does not honour them — treat the resource as builder-only.

```php
use Samsara\Facades\Samsara;

$intervals = Samsara::speeding()
    ->intervalsStream()
    ->between(now()->subDays(7), now())
    ->get();

foreach ($intervals as $interval) {
    SpeedingReport::record($interval->toArray());
}
```

For long windows, prefer the `lazy()` cursor:

```php
Samsara::speeding()
    ->intervalsStream()
    ->between(now()->subMonth(), now())
    ->lazy(500)
    ->each(fn ($interval) => SpeedingReport::record($interval->toArray()));
```

## Filtering

Refer to the [query builder](../query-builder.md) for the full operator reference. The speeding builder commonly accepts:

```php
$intervals = Samsara::speeding()
    ->intervalsStream()
    ->whereVehicle('vehicle-id')
    ->whereDriver('driver-id')
    ->whereTag('delivery-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```

A `between()` window is required by the upstream endpoint.

## Properties

`SpeedingResource` does not declare a typed `$entity`, so each result is a generic `Samsara\Data\Entity` (a `Fluent` instance). The keys returned by the API are typically:

| Key | Type | Description |
|-----|------|-------------|
| `vehicle` | `array{id?: string, name?: string}` | Vehicle that was speeding. |
| `driver` | `array{id?: string, name?: string}` | Driver assigned to the vehicle. |
| `startMs` | `int` | Interval start in milliseconds since the Unix epoch. |
| `endMs` | `int` | Interval end in milliseconds since the Unix epoch. |
| `maxSpeedMph` | `float` | Peak speed during the interval. |
| `durationMs` | `int` | Interval duration in milliseconds. |
| `severity` | `string` | Severity bucket (`light`, `moderate`, `heavy`, `severe`). |

Read keys with array access (`$interval['maxSpeedMph']`) or magic-property access (`$interval->maxSpeedMph`). No helper methods are available because no typed entity is bound.

## Related Resources

- [Safety Events](safety-events.md) — coaching events that complement the speeding stream.
- [Vehicles](vehicles.md) — resolve `$interval->vehicle['id']`.
- [Drivers](drivers.md) — resolve `$interval->driver['id']`.
- [Error Handling](../error-handling.md) — exceptions raised by the underlying request.

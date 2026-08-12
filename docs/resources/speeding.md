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

The `Samsara::speeding()` resource exposes the `/speeding-intervals/stream` feed of intervals during which a vehicle exceeded the posted limit. It is read-only and returns generic `Entity` instances, since `SpeedingResource` does not declare a typed `$entity`. Pair it with [Safety Events](safety-events.html) when you build coaching dashboards or monthly safety reports.

## Retrieving Records

Speeding intervals are streamed through the `intervalsStream()` builder. All generic CRUD operations are rejected before HTTP.

```php
use Samsara\Facades\Samsara;

$trips = Samsara::speeding()
    ->intervalsStream()
    ->where('assetIds', 'vehicle-id')
    ->between(now()->subDays(7), now())
    ->get();

foreach ($trips as $trip) {
    SpeedingReport::record($trip->toArray());
}
```

For long windows, prefer the `lazy()` cursor:

```php
Samsara::speeding()
    ->intervalsStream()
    ->where('assetIds', 'vehicle-id')
    ->between(now()->subMonth(), now())
    ->lazy()
    ->each(fn ($trip) => SpeedingReport::record($trip->toArray()));
```

## Filtering

Refer to the [query builder](../query-builder.html) for the full operator reference. The speeding builder commonly accepts:

```php
$trips = Samsara::speeding()
    ->intervalsStream()
    ->where('assetIds', 'vehicle-id')
    ->where('includeDriverId', true)
    ->between(now()->subDays(7), now())
    ->get();
```

The upstream endpoint requires `assetIds` and `startTime`; `endTime` is optional. `between()` supplies both timestamps.

## Properties

`SpeedingResource` does not declare a typed `$entity`, so each result is a generic `Samsara\Data\Entity` (a `Fluent` instance). Each result represents one trip and contains nested `intervals`:

| Key | Type | Description |
|-----|------|-------------|
| `asset` | `array` | Asset associated with the trip. |
| `driverId` | `?string` | Driver ID when `includeDriverId` is requested and a driver is known. |
| `tripStartTime` | `string` | RFC 3339 trip start time. |
| `createdAtTime` | `string` | RFC 3339 creation time. |
| `updatedAtTime` | `string` | RFC 3339 last-update time. |
| `intervals` | `array` | Nested speeding intervals for the trip. |

Each nested interval may include `startTime`, `endTime`, `isDismissed`, `location`, `maxSpeedKilometersPerHour`, `postedSpeedLimitKilometersPerHour`, and `severityLevel`.

Read parent keys with array access (`$trip['intervals']`) or magic-property access (`$trip->intervals`). No helper methods are available because no typed entity is bound.

## Related Resources

- [Safety Events](safety-events.html) — coaching events that complement the speeding stream.
- [Vehicles](vehicles.html) — resolve the trip's `asset` reference.
- [Drivers](drivers.html) — resolve the optional `driverId`.
- [Error Handling](../error-handling.html) — exceptions raised by the underlying request.

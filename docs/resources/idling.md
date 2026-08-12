---
title: Idling
nav_order: 25
description: Vehicle idling events for fuel and compliance reporting.
permalink: /resources/idling
---

# Idling

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::idling()` resource exposes the `/idling/events` stream so you may report on vehicles left running while parked. It is builder-only: all generic CRUD operations are rejected. Rows are generic `Entity` instances. Reach for it when you build fuel-waste reports, idle-time leaderboards, or notify-on-idle alerts that supplement the [Alerts](alerts.html) resource.

## Retrieving Records

Idling events are streamed through the `events()` builder. The base `find()`, `create()`, `update()`, and `delete()` methods on the resource are inherited from the SDK base class but the upstream endpoint does not honour them — treat the resource as builder-only.

```php
use Samsara\Facades\Samsara;

$events = Samsara::idling()
    ->events()
    ->between(now()->subDays(7), now())
    ->get();

foreach ($events as $event) {
    logger()->info('Idle event', $event->toArray());
}
```

Stream large windows with the `lazy()` cursor instead of buffering everything in memory:

```php
Samsara::idling()
    ->events()
    ->between(now()->subMonth(), now())
    ->lazy(500)
    ->each(fn ($event) => IdleReport::record($event->toArray()));
```

## Filtering

The full set of operators lives on the [query builder](../query-builder.html) page. The idling builder commonly accepts:

```php
$events = Samsara::idling()
    ->events()
    ->where('assetIds', 'vehicle-id')
    ->whereTag('delivery-fleet')
    ->between(now()->subDays(7), now())
    ->limit(100)
    ->get();
```

A `between()` window is required by the upstream endpoint. Omitting it leaves validation to the API and may raise the SDK's mapped validation exception.

## Properties

`IdlingResource` does not declare a typed `$entity`, so each result is a generic `Samsara\Data\Entity` (a `Fluent` instance). The keys returned by the API are typically:

| Key | Type | Description |
|-----|------|-------------|
| `asset` | `array` | Asset that idled. |
| `startTime` | `string` | RFC 3339 start of the idle interval. |
| `durationMilliseconds` | `int` | Idle duration in milliseconds. |
| `eventUuid` | `string` | Event UUID. |
| `fuelConsumedMilliliters` | `int` | Liquid fuel consumed during the event. |
| `fuelCost` | `array` | Liquid-fuel cost details. |
| `gaseousFuelConsumedGrams` | `int` | Gaseous fuel consumed during the event. |
| `gaseousFuelCost` | `array` | Gaseous-fuel cost details. |
| `ptoState` | `string` | Power-take-off state. |
| `address` | `?string` | Reverse-geocoded address, when available. |
| `airTemperatureMillicelsius` | `?int` | Ambient air temperature, when available. |
| `latitude` / `longitude` | `?float` | Event coordinates, when available. |
| `operator` | `?array` | Operator details, when available. |

Read keys with array access (`$event['durationMilliseconds']`) or magic-property access (`$event->durationMilliseconds`). Helper methods are not available because no typed entity is bound.

## Related Resources

- [Vehicles](vehicles.html) — resolve the vehicle from `$event->asset['id']`.
- [Vehicle Stats](vehicle-stats.html) — pair idle intervals with `engineStates` history.
- [Speeding](speeding.html) — companion stream for behaviour reporting.
- [Error Handling](../error-handling.html) — exceptions raised by the underlying request.

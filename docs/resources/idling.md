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

The `Samsara::idling()` resource exposes the `/idling/events` stream so you may report on vehicles left running while parked. It is read-only and returns generic `Entity` instances (a `Fluent` wrapper around the API payload), since `IdlingResource` does not declare a typed `$entity`. Reach for it when you build fuel-waste reports, idle-time leaderboards, or notify-on-idle alerts that supplement the [Alerts](alerts.md) resource.

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

The full set of operators lives on the [query builder](../query-builder.md) page. The idling builder commonly accepts:

```php
$events = Samsara::idling()
    ->events()
    ->whereVehicle('vehicle-id')
    ->whereTag('delivery-fleet')
    ->between(now()->subDays(7), now())
    ->limit(100)
    ->get();
```

A `between()` window is required by the upstream endpoint; calls without one return an empty collection.

## Properties

`IdlingResource` does not declare a typed `$entity`, so each result is a generic `Samsara\Data\Entity` (a `Fluent` instance). The keys returned by the API are typically:

| Key | Type | Description |
|-----|------|-------------|
| `vehicle` | `array{id?: string, name?: string}` | Vehicle that idled. |
| `startTime` | `string` | RFC 3339 start of the idle interval. |
| `endTime` | `string` | RFC 3339 end of the idle interval. |
| `durationMs` | `int` | Idle duration in milliseconds. |
| `location` | `array{latitude?: float, longitude?: float}` | Idle location. |

Read keys with array access (`$event['durationMs']`) or magic-property access (`$event->durationMs`). Helper methods are not available because no typed entity is bound.

## Related Resources

- [Vehicles](vehicles.md) — resolve the vehicle from `$event->vehicle['id']`.
- [Vehicle Stats](vehicle-stats.md) — pair idle intervals with `engineStates` history.
- [Speeding](speeding.md) — companion stream for behaviour reporting.
- [Error Handling](../error-handling.md) — exceptions raised by the underlying request.

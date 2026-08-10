---
title: Route Events
nav_order: 35
description: Stream of route lifecycle events for dispatch operations.
permalink: /resources/route-events
---

# Route Events

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::routeEvents()` resource exposes lifecycle events emitted as drivers progress through dispatched routes. `RouteEventsResource` declares `$entity = Entity::class`, so each result is a generic `Fluent` instance.

`query()` and the supported generic `all()` target `GET /route-events/stream`. Other generic operations are rejected before HTTP.

## Retrieving Records

Use the `query()` builder for filters and cursors. Route events are emitted by Samsara, so `find()`, `create()`, `update()`, and `delete()` are unsupported.

```php
use Samsara\Facades\Samsara;

$events = Samsara::routeEvents()
    ->query()
    ->between(now()->subDays(7), now())
    ->get();

foreach ($events as $event) {
    RouteEvent::record($event->toArray());
}
```

For long windows, prefer the `lazy()` cursor:

```php
Samsara::routeEvents()
    ->query()
    ->between(now()->subMonth(), now())
    ->lazy(500)
    ->each(fn ($event) => RouteEvent::record($event->toArray()));
```

## Filtering

Refer to the [query builder](../query-builder.html) for the full operator reference. The route-events stream commonly accepts:

```php
$events = Samsara::routeEvents()
    ->query()
    ->whereTag('delivery-routes')
    ->between(now()->subDays(7), now())
    ->limit(100)
    ->get();
```

A `between()` window is required by the upstream endpoint.

## Properties

`RouteEventsResource` returns generic `Samsara\Data\Entity` instances. The keys returned by the API are typically:

| Key | Type | Description |
|-----|------|-------------|
| `id` | `string` | Event ID. |
| `eventType` | `string` | Event type (`routeStarted`, `stopArrived`, `stopDeparted`, etc.). |
| `time` | `string` | RFC 3339 event timestamp. |
| `route` | `array{id?: string, name?: string}` | Route the event belongs to. |
| `stop` | `array{id?: string, name?: string}` | Stop the event refers to (when applicable). |
| `vehicle` | `array{id?: string, name?: string}` | Vehicle reporting the event. |
| `driver` | `array{id?: string, name?: string}` | Driver reporting the event. |

Read keys with array access (`$event['eventType']`) or magic-property access (`$event->eventType`). No helper methods are available, since no typed entity is bound.

## Related Resources

- [Routes](routes.html) — the parent route entity.
- [Trips](trips.html) — vehicle-centric trip data that overlaps with route events.
- [Webhooks](webhooks.html) — push delivery for route lifecycle changes.
- [Error Handling](../error-handling.html) — exceptions raised by the underlying request.

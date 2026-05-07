---
title: Routes
nav_order: 11
description: Dispatched routes with stops, schedule, and audit logs.
permalink: /resources/routes
---

# Routes

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Audit Logs](#audit-logs)
- [Filtering](#filtering)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
  - [Route Stops](#route-stops)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::routes()` resource manages dispatched routes — ordered sets of stops scheduled against a hub or plan. Each route exposes its scheduled window, distance, duration, and an array of stop entries; the typed `Route` entity also surfaces helpers for unit conversion and pinned/edited flags. The lifecycle events emitted as drivers run a route live on the [Route Events](route-events.md) resource.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$route = Samsara::routes()->find('route-id');

$routes = Samsara::routes()->all();

$weekly = Samsara::routes()
    ->query()
    ->between(now()->startOfWeek(), now()->endOfWeek())
    ->get();
```

`find()` returns `null` when the route does not exist; `all()` and the builder return an `EntityCollection<int, Route>`.

## Creating Records

```php
$route = Samsara::routes()->create([
    'name' => 'Delivery Route A',
    'scheduledRouteStartTime' => now()->addHour()->toIso8601String(),
    'scheduledRouteEndTime' => now()->addHours(8)->toIso8601String(),
    'stops' => [
        [
            'name' => 'Pickup',
            'scheduledArrivalTime' => now()->addHour()->toIso8601String(),
            'singleUseLocation' => [
                'address' => '123 Market St, San Francisco, CA',
                'latitude' => 37.7749,
                'longitude' => -122.4194,
            ],
        ],
        [
            'name' => 'Drop-off',
            'scheduledArrivalTime' => now()->addHours(3)->toIso8601String(),
            'singleUseLocation' => [
                'address' => '500 Terry A Francois Blvd, San Francisco, CA',
                'latitude' => 37.7702,
                'longitude' => -122.3879,
            ],
        ],
    ],
]);
```

`create()` returns the saved `Route`.

## Updating Records

```php
$route = Samsara::routes()->update('route-id', [
    'name' => 'Updated Delivery Route',
]);
```

## Deleting Records

```php
Samsara::routes()->delete('route-id');
```

## Audit Logs

`auditLogs()` returns a builder rooted at `/fleet/routes/audit-logs/feed` for tracking changes to dispatched routes:

```php
$logs = Samsara::routes()
    ->auditLogs()
    ->between(now()->subDays(7), now())
    ->get();
```

The audit-log stream returns generic `Entity` instances since `auditLogs()` reuses the resource's `$entity` declaration only nominally — the payload shape is the audit-log shape, not a `Route`.

## Filtering

Refer to the [query builder](../query-builder.md) for the full operator reference. The routes builder commonly accepts:

```php
$routes = Samsara::routes()
    ->query()
    ->whereTag('delivery-routes')
    ->between(now(), now()->addDays(7))
    ->limit(50)
    ->get();
```

The companion `RouteState` enum (`CANCELLED`, `COMPLETED`, `IN_PROGRESS`, `NOT_STARTED`, `SKIPPED`) maps the lifecycle states reported by Samsara — see [Enums](../enums.md#routestate) for the full case-to-value mapping.

## Helper Methods

The `Route` entity exposes the following helpers:

```php
$route = Samsara::routes()->find('route-id');

$route->getDisplayName();         // string — falls back to 'Unknown'
$route->getDistanceMiles();       // ?float — distanceMeters / 1609.344
$route->getDistanceKilometers();  // ?float — distanceMeters / 1000
$route->getDurationHours();       // ?float — durationSeconds / 3600
$route->getDurationMinutes();     // ?float — durationSeconds / 60
$route->getStops();               // array<int, RouteStop> — typed wrappers
$route->getStopCount();           // int
$route->getSettings();            // ?RouteSettings
$route->isEdited();               // bool
$route->isPinned();               // bool
```

## Properties

The `Route` entity exposes the following `@property-read` keys:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Route ID (UUID). |
| `name` | `?string` | Route name. |
| `type` | `?string` | Route type (e.g., `dynamic`). |
| `hubId` | `?string` | Hub ID this route belongs to. |
| `planId` | `?string` | Plan ID this route belongs to. |
| `dispatchRouteId` | `?string` | Dispatch route identifier. |
| `cost` | `?float` | Route cost. |
| `distanceMeters` | `?int` | Total distance in meters. |
| `durationSeconds` | `?int` | Total duration in seconds. |
| `orgLocationTimezone` | `?string` | Organization location timezone. |
| `scheduledRouteStartTime` | `?string` | Scheduled start time (RFC 3339). |
| `scheduledRouteEndTime` | `?string` | Scheduled end time (RFC 3339). |
| `createdAt` | `?string` | Creation timestamp. |
| `updatedAt` | `?string` | Last update timestamp. |
| `isEdited` | `?bool` | Whether the route has been edited. |
| `isPinned` | `?bool` | Whether the route is pinned. |
| `stops` | `?array<int, array>` | Route stops (see [Route Stops](#route-stops)). |
| `quantities` | `?array<int, array{name?: string, value?: float\|int}>` | Per-route quantity totals. |
| `settings` | `?array{routeCompletionCondition?: string, routeStartingCondition?: string, sequencingMethod?: string}` | Route settings. |

> **Note:** The entity does not expose flat `driverId`, `vehicleId`, `actualStartTime`, `actualEndTime`, `status`, or `notes` keys. Driver and vehicle data live on the per-stop payload returned by the upstream API; lifecycle state lives on the [Route Events](route-events.md) stream.

### Route Stops

`$route->stops` is an array of stop payloads. Use `$route->getStops()` to wrap each into a typed `RouteStop` entity:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Stop ID (UUID). |
| `name` | `?string` | Stop name. |
| `hubLocationId` | `?string` | Hub location identifier. |
| `notes` | `?string` | Additional notes for the stop. |
| `scheduledArrivalTime` | `?string` | Scheduled arrival (RFC 3339). |
| `scheduledDepartureTime` | `?string` | Scheduled departure (RFC 3339). |
| `orders` | `?array<int, array{id?: string, description?: string}>` | Order tasks at this stop. |
| `singleUseLocation` | `?array{address?: string, latitude?: float, longitude?: float}` | Ad-hoc location. |

`RouteStop` adds the helpers `getDisplayName()`, `getOrderCount()`, `hasHubLocation()`, and `hasSingleUseLocation()`.

## Related Resources

- [Route Events](route-events.md) — lifecycle events emitted as drivers run a route.
- [Addresses](addresses.md) — referenced via `hubLocationId` on each stop.
- [Hubs](hubs.md) — the hub that owns a route.
- [Trips](trips.md) — the vehicle-centric record of the same drive.
- [Enums](../enums.md#routestate) — `RouteState` reference.

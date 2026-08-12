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

The `Samsara::routes()` resource manages dispatched routes and their ordered stops. Route-level data includes scheduled and actual windows, assigned driver and vehicle references, settings, and stop entries. Stop payloads carry their own state plus actual and planned distance from the preceding stop. Lifecycle events emitted as drivers run a route live on the [Route Events](route-events.html) resource.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$route = Samsara::routes()->find('route-id');

$weekly = Samsara::routes()
    ->query()
    ->between(now()->startOfWeek(), now()->endOfWeek())
    ->get();
```

`find()` returns `null` when the route does not exist. Collection reads must use `query()` with the required time window; generic `all()` is unsupported and fails locally. Generic `find`, `create`, `update`, and `delete` remain supported.

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

Audit-log rows are mapped to `Samsara\Data\Route\RouteAuditLog`. The builder suppresses the implicit page size because the feed accepts cursors but no `limit`.

## Filtering

Refer to the [query builder](../query-builder.html) for the full operator reference. The routes builder commonly accepts:

```php
$routes = Samsara::routes()
    ->query()
    ->whereTag('delivery-routes')
    ->between(now(), now()->addDays(7))
    ->limit(50)
    ->get();
```

The package also exposes a `RouteState` enum (`CANCELLED`, `COMPLETED`, `IN_PROGRESS`, `NOT_STARTED`, `SKIPPED`). The current upstream route schema does not expose a matching top-level state field; route-stop states use a different value set. Do not treat this enum as a current upstream wire contract without validating the endpoint payload.

## Helper Methods

The `Route` entity exposes the following helpers:

```php
$route = Samsara::routes()->find('route-id');

$route->getDisplayName();         // string — falls back to 'Unknown'
$route->getStops();               // array<int, RouteStop> — typed wrappers
$route->getStopCount();           // int
$route->getSettings();            // ?RouteSettings
```

Route-level distance, duration, edited, and pinned helpers are intentionally absent because the current `/fleet/routes` response does not provide those fields.

## Properties

The `Route` entity exposes the following `@property-read` keys:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Route ID (UUID). |
| `name` | `?string` | Route name. |
| `notes` | `?string` | Route notes. |
| `orgLocalTimezone` | `?string` | Organization-local timezone. |
| `actualRouteStartTime` | `?string` | Actual start time (RFC 3339). |
| `actualRouteEndTime` | `?string` | Actual end time (RFC 3339). |
| `scheduledRouteStartTime` | `?string` | Scheduled start time (RFC 3339). |
| `scheduledRouteEndTime` | `?string` | Scheduled end time (RFC 3339). |
| `driver` | `?array{id?: string, name?: string}` | Assigned driver. |
| `vehicle` | `?array{id?: string, name?: string}` | Assigned vehicle. |
| `externalIds` | `?array<string, string>` | External ID mappings. |
| `tagIds` | `?array<int, string>` | Tag IDs. |
| `recurringRouteLiveSharingLinks` | `?array<int, array<string, mixed>>` | Live-sharing links. |
| `stops` | `?array<int, array>` | Route stops (see [Route Stops](#route-stops)). |
| `settings` | `?array{routeCompletionCondition?: string, routeStartingCondition?: string, sequencingMethod?: string}` | Route settings. |

> **Note:** The entity uses nested `driver` and `vehicle` references plus `actualRouteStartTime` and `actualRouteEndTime`; it does not expose the legacy flat IDs, generic `actualStartTime` / `actualEndTime`, or a top-level lifecycle `status`. Lifecycle state lives on the [Route Events](route-events.html) stream.

### Route Stops

`$route->stops` is an array of stop payloads. Use `$route->getStops()` to wrap each into a typed `RouteStop` entity:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Stop ID (UUID). |
| `name` | `?string` | Stop name. |
| `state` | `?string` | `unassigned`, `scheduled`, `en route`, `skipped`, `arrived`, or `departed`. |
| `notes` | `?string` | Additional notes for the stop. |
| `address` | `?array<string, mixed>` | Stop address. |
| `actualDistanceMeters` | `?int` | Actual distance from the prior stop. |
| `plannedDistanceMeters` | `?int` | Planned distance from the prior stop. |
| `actualArrivalTime` | `?string` | Actual arrival time (RFC 3339). |
| `actualDepartureTime` | `?string` | Actual departure time (RFC 3339). |
| `scheduledArrivalTime` | `?string` | Scheduled arrival (RFC 3339). |
| `scheduledDepartureTime` | `?string` | Scheduled departure (RFC 3339). |
| `sequenceNumber` | `?int` | Stop sequence number. |
| `orders` | `?array<int, array{id?: string, description?: string}>` | Order tasks at this stop. |
| `singleUseLocation` | `?array{address?: string, latitude?: float, longitude?: float}` | Ad-hoc location. |

`RouteStop` exposes `getDisplayName()`, `getOrderCount()`, and `hasSingleUseLocation()`, plus exact state helpers: `isUnassigned()`, `isScheduled()`, `isEnRoute()`, `isSkipped()`, `isArrived()`, and `isDeparted()`.

Use `getActualDistanceKilometers()`, `getActualDistanceMiles()`, `getPlannedDistanceKilometers()`, and `getPlannedDistanceMiles()` for unit conversion. Missing distance returns `null`; zero meters returns `0.0`. `hasHubLocation()` remains absent because this payload has no `hubLocationId`.

## Related Resources

- [Route Events](route-events.html) — lifecycle events emitted as drivers run a route.
- [Addresses](addresses.html) — reusable organization locations that can inform route planning.
- [Trips](trips.html) — the vehicle-centric record of the same drive.
- [Enums](../enums.html#routestate) — `RouteState` reference.

---
title: Safety Events
nav_order: 10
description: Driver safety events with video, coaching state, and behavior labels.
permalink: /resources/safety-events
---

# Safety Events

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Audit Logs](#audit-logs)
- [Event Types](#event-types)
- [Filtering](#filtering)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::safetyEvents()` resource exposes Samsara's catalog of driver safety events — harsh braking, distracted driving, crashes, and similar incidents — together with the coaching state and downloadable video clips attached to each one. Reach for this resource to power scorecards, coaching workflows, and incident reviews.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$event = Samsara::safetyEvents()->find('event-id');

$weekly = Samsara::safetyEvents()
    ->query()
    ->between(now()->subDays(7), now())
    ->get();
```

For long windows, prefer the `lazy()` cursor:

```php
Samsara::safetyEvents()
    ->query()
    ->between(now()->subYear(), now())
    ->lazy(500)
    ->each(fn ($event) => SafetyEventReport::record($event->toArray()));
```

The base `create()`, `update()`, and `delete()` methods are inherited from the SDK base class but the upstream endpoint does not honour them — safety events are emitted by Samsara's hardware, not authored by your application.

## Audit Logs

`auditLogs()` returns a builder rooted at `/fleet/safety-events/audit-logs/feed` for tracking review and coaching activity:

```php
$logs = Samsara::safetyEvents()
    ->auditLogs()
    ->between(now()->subDays(7), now())
    ->get();
```

## Event Types

Filter by `eventTypes` using the string values backed by the `SafetyEventType` enum (20 cases):

| Case | Value |
|------|-------|
| `CAMERA_OBSTRUCTION` | `cameraObstruction` |
| `CELL_PHONE_USAGE` | `cellPhoneUsage` |
| `CRASH` | `crash` |
| `DEFENSIVE_DRIVING` | `defensiveDriving` |
| `DISTRACTED_DRIVING` | `distractedDriving` |
| `DRIVER_DETECTED` | `driverDetected` |
| `DROWSY_DRIVING` | `drowsyDriving` |
| `FOLLOWING_DISTANCE` | `followingDistance` |
| `HARSH_ACCELERATION` | `harshAcceleration` |
| `HARSH_BRAKING` | `harshBraking` |
| `HARSH_TURN` | `harshTurn` |
| `LANE_DEPARTURE` | `laneDeparture` |
| `MAX_SPEED` | `maxSpeed` |
| `NEAR_COLLISION` | `nearCollision` |
| `NO_DRIVER_DETECTED` | `noDriverDetected` |
| `ROLLING_STOP` | `rollingStop` |
| `SEATBELT` | `seatbelt` |
| `SMOKING` | `smoking` |
| `SPEEDING` | `speeding` |
| `UNKNOWN` | `unknown` |

See [Enums](../enums.md#safetyeventtype) for the canonical reference.

## Filtering

Refer to the [query builder](../query-builder.md) for the full operator reference. The safety-events builder commonly accepts:

```php
$events = Samsara::safetyEvents()
    ->query()
    ->whereVehicle('vehicle-id')
    ->whereDriver('driver-id')
    ->whereTag('fleet-west')
    ->between(now()->subDays(30), now())
    ->limit(100)
    ->get();
```

A `between()` window is required by the upstream endpoint.

## Helper Methods

The `SafetyEvent` entity exposes one helper:

```php
$event = Samsara::safetyEvents()->find('event-id');

$event->hasVideo();  // bool — true if any of the three video URLs are present
```

`hasVideo()` returns `true` when any of `downloadForwardVideoUrl`, `downloadInwardVideoUrl`, or `downloadTrackedInwardVideoUrl` are populated.

## Properties

The `SafetyEvent` entity exposes the following `@property-read` keys:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Safety event ID. |
| `time` | `?string` | RFC 3339 event timestamp. |
| `coachingState` | `?string` | Coaching state for the event. |
| `maxAccelerationGForce` | `?float` | Peak acceleration during the event in G-force. |
| `downloadForwardVideoUrl` | `?string` | Forward-facing video URL. |
| `downloadInwardVideoUrl` | `?string` | Inward-facing video URL. |
| `downloadTrackedInwardVideoUrl` | `?string` | Tracked inward video URL. |
| `driver` | `?array{id?: string, name?: string}` | Driver involved in the event. |
| `vehicle` | `?array{id?: string, name?: string}` | Vehicle involved in the event. |
| `location` | `?array{latitude?: float, longitude?: float}` | Event location. |
| `behaviorLabels` | `?array<int, array{label?: string, source?: string}>` | Behavior labels assigned to the event. |

> **Note:** The entity does not expose flat `driverId`, `vehicleId`, `eventType`, `behaviorLabel`, `maxSpeedMph`, or `durationMs` keys. Read the IDs via `$event->driver['id']` and `$event->vehicle['id']`; behavior labels live in the `behaviorLabels` array.

## Related Resources

- [Drivers](drivers.md) — resolve `$event->driver['id']`.
- [Vehicles](vehicles.md) — resolve `$event->vehicle['id']`.
- [Camera Media](camera-media.md) — additional video footage tied to a vehicle.
- [Speeding](speeding.md) — companion stream for behaviour reporting.
- [Enums](../enums.md#safetyeventtype) — `SafetyEventType` reference.
- [Error Handling](../error-handling.md) — exceptions raised by the underlying request.

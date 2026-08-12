---
title: Assignments
nav_order: 34
description: Read driver-vehicle, driver-trailer, and trailer assignments.
permalink: /resources/assignments
---

# Assignments

- [Introduction](#introduction)
- [Driver-Vehicle Assignments](#driver-vehicle-assignments)
- [Driver-Trailer Assignments](#driver-trailer-assignments)
- [Trailer Assignments (Legacy)](#trailer-assignments-legacy)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Assignments record which driver was paired with which vehicle or trailer over time. The SDK exposes four facade accessors: `driverVehicleAssignments()`, `driverTrailerAssignments()`, `trailerAssignments()`, and `carrierProposedAssignments()`. Reach for these resources when you need to reconstruct an audit trail of who drove what, or who pulled which trailer, over a given window. The driver-vehicle, driver-trailer, and carrier-proposed builders return generic `Samsara\Data\Entity` (`Fluent`) rows; the legacy `trailerAssignments()->forTrailer()` helper returns a typed `Trailer` wrapper.

Driver-vehicle and driver-trailer resources support generic `create` plus their read builders; all other generic verbs are rejected. The legacy trailer-assignment resource supports only `forTrailer()`.

For carrier-proposed driver-vehicle assignments, see the [Carrier Proposed Assignments](carrier-proposed-assignments.html) page.

## Driver-Vehicle Assignments

`driverVehicleAssignments()` returns a query builder you can filter by driver, vehicle, or time range.

```php
use Samsara\Facades\Samsara;

$forDriver = Samsara::driverVehicleAssignments()
    ->query()
    ->where('filterBy', 'drivers')
    ->whereDriver('driver-123')
    ->get();

$forVehicle = Samsara::driverVehicleAssignments()
    ->query()
    ->where('filterBy', 'vehicles')
    ->whereVehicle('vehicle-456')
    ->get();
```

`filterBy` is required and must match the ID filter you supply.

## Driver-Trailer Assignments

The driver-trailer endpoint mirrors the driver-vehicle one.

```php
$forDriver = Samsara::driverTrailerAssignments()
    ->query()
    ->whereDriver('driver-123')
    ->get();
```

The driver-trailer operation requires at least one `driverIds` value, which `whereDriver()` supplies.

## Trailer Assignments (Legacy)

> **Note:** Legacy v1 endpoint. Prefer driver-trailer assignments for new integrations.

The legacy resource exposes a single helper that returns the assignment history for a single trailer.

```php
$trailer = Samsara::trailerAssignments()
    ->forTrailer(123, ['startMs' => $startMs, 'endMs' => $endMs]);

$assignments = $trailer->trailerAssignments;
```

## Properties

The current assignment builders return generic `Samsara\Data\Entity` rows. `forTrailer(int $trailerId, array $params = []): Trailer` returns one typed legacy `Trailer` wrapper containing `trailerAssignments`.

| Key | Description |
|-----|-------------|
| `driver` | `{id, name?}` — the assigned driver. |
| `vehicle` | `{id, name?}` — the assigned vehicle (driver-vehicle only). |
| `trailer` | `{id, name?}` — the assigned trailer (driver-trailer / trailer only). |
| `startTime` | RFC 3339 start timestamp. |
| `endTime` | RFC 3339 end timestamp (when the assignment closed). |

Read each key directly from the entity (`$row->driver`) or via `$row->get('key')`.

## Related Resources

- [Drivers](drivers.html) — the driver side of every assignment.
- [Vehicles](vehicles.html) — the vehicle side of driver-vehicle assignments.
- [Trailers](trailers.html) — the trailer side of driver-trailer assignments.
- [Carrier Proposed Assignments](carrier-proposed-assignments.html) — proposed driver-vehicle assignments awaiting confirmation.
- [Query Builder](../query-builder.html) — for filtering, pagination, and lazy iteration.

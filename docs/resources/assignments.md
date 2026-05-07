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

Assignments record which driver was paired with which vehicle or trailer over time. The SDK exposes four facade accessors: `driverVehicleAssignments()`, `driverTrailerAssignments()`, `trailerAssignments()`, and `carrierProposedAssignments()`. Reach for these resources when you need to reconstruct an audit trail of who drove what, or who pulled which trailer, over a given window. None of the four resources declare a typed entity — each returns generic `Samsara\Data\Entity` (`Fluent`) instances.

For carrier-proposed driver-vehicle assignments, see the [Carrier Proposed Assignments](carrier-proposed-assignments.md) page.

## Driver-Vehicle Assignments

`driverVehicleAssignments()` returns a query builder you can filter by driver, vehicle, or time range.

```php
use Samsara\Facades\Samsara;

$assignments = Samsara::driverVehicleAssignments()
    ->query()
    ->get();

$forDriver = Samsara::driverVehicleAssignments()
    ->query()
    ->whereDriver('driver-123')
    ->get();

$forVehicle = Samsara::driverVehicleAssignments()
    ->query()
    ->whereVehicle('vehicle-456')
    ->get();
```

## Driver-Trailer Assignments

The driver-trailer endpoint mirrors the driver-vehicle one.

```php
$assignments = Samsara::driverTrailerAssignments()
    ->query()
    ->get();

$forDriver = Samsara::driverTrailerAssignments()
    ->query()
    ->whereDriver('driver-123')
    ->get();
```

## Trailer Assignments (Legacy)

> **Note:** Legacy v1 endpoint. Prefer driver-trailer assignments for new integrations.

The legacy resource exposes a single helper that returns the assignment history for a single trailer.

```php
$assignments = Samsara::trailerAssignments()
    ->forTrailer('trailer-123');
```

## Properties

None of these four resources declare a typed entity. Records come back as generic `Samsara\Data\Entity` instances. The fields you can read off each row depend on the assignment kind — typical keys include:

| Key | Description |
|-----|-------------|
| `driver` | `{id, name?}` — the assigned driver. |
| `vehicle` | `{id, name?}` — the assigned vehicle (driver-vehicle only). |
| `trailer` | `{id, name?}` — the assigned trailer (driver-trailer / trailer only). |
| `startTime` | RFC 3339 start timestamp. |
| `endTime` | RFC 3339 end timestamp (when the assignment closed). |

Read each key directly from the entity (`$row->driver`) or via `$row->get('key')`.

## Related Resources

- [Drivers](drivers.md) — the driver side of every assignment.
- [Vehicles](vehicles.md) — the vehicle side of driver-vehicle assignments.
- [Trailers](trailers.md) — the trailer side of driver-trailer assignments.
- [Carrier Proposed Assignments](carrier-proposed-assignments.md) — proposed driver-vehicle assignments awaiting confirmation.
- [Query Builder](../query-builder.md) — for filtering, pagination, and lazy iteration.

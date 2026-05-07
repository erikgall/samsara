---
title: Maintenance
nav_order: 9
description: Driver Vehicle Inspection Reports and defect tracking.
permalink: /resources/maintenance
---

# Maintenance

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Defects](#defects)
- [Filtering](#filtering)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::maintenance()` resource exposes Driver Vehicle Inspection Reports (DVIRs) and the defects logged against them. DVIRs are the federally mandated pre-trip and post-trip inspections drivers perform; defects are the individual issues reported on each one. The resource is typed against `Dvir`; the defects builder is also typed as `Dvir` due to a single `$entity` declaration on the resource (see [Defects](#defects)).

## Retrieving Records

The default resource endpoint is `/dvirs/stream`, so calling `Samsara::maintenance()->all()` (or the equivalent `dvirs()->get()`) returns the DVIR stream rather than a snapshot list.

```php
use Samsara\Facades\Samsara;

$dvir = Samsara::maintenance()->find('dvir-id');

$dvirs = Samsara::maintenance()
    ->dvirs()
    ->between(now()->subDays(7), now())
    ->get();

$updated = Samsara::maintenance()
    ->dvirs()
    ->updatedAfter(now()->subHour())
    ->get();
```

For long windows, prefer the `lazy()` cursor:

```php
Samsara::maintenance()
    ->dvirs()
    ->between(now()->subYear(), now())
    ->lazy(500)
    ->each(fn ($dvir) => DvirReport::record($dvir->toArray()));
```

`dvirs()` and `query()` both return a fresh `Builder` rooted at `/dvirs/stream`; use whichever reads better in context.

## Creating Records

`createDvir()` posts to `/dvirs` and returns the saved `Dvir` entity. The payload mirrors the entity's `@property-read` shape — note that the upstream API uses `type` (not `inspectionType`), splits defects into `vehicleDefects` and `trailerDefects`, and accepts a single `trailer` (not `trailerIds`):

```php
$dvir = Samsara::maintenance()->createDvir([
    'vehicle' => ['id' => 'vehicle-id'],
    'type' => 'preTrip',
    'safetyStatus' => 'safe',
    'startTime' => now()->toIso8601String(),
    'endTime' => now()->addMinutes(10)->toIso8601String(),
    'odometerMeters' => 184_523,
    'vehicleDefects' => [
        [
            'defectType' => 'Brakes',
            'comment' => 'Brake pad worn',
        ],
    ],
]);
```

## Updating Records

`Samsara::maintenance()->update('dvir-id', $data)` is inherited from the base resource and patches `/dvirs/stream/{id}`. Most workflows create a fresh DVIR rather than updating an old one; verify the upstream endpoint accepts a PATCH for your use case before relying on it.

## Deleting Records

DVIRs are records of completed inspections and are not designed to be deleted. The base `delete()` method is inherited but the upstream endpoint typically rejects it.

> **Note:** Treat DVIRs as append-only. Resolve safety issues by uploading a follow-up inspection, not by deleting the old one.

## Defects

Defects are exposed through the `defects()` builder, which targets `/defects/stream`:

```php
$defects = Samsara::maintenance()
    ->defects()
    ->between(now()->subDays(30), now())
    ->whereVehicle('vehicle-id')
    ->get();
```

> **Note:** This builder is typed as `Dvir` due to the resource's single `$entity` declaration. The properties below describe the actual `Defect` API response shape, but the typed `Defect` helpers (`isResolved()`, etc.) are not invoked when accessing the result. Calling `$result->isResolved()` on a defect returned by this builder resolves to `Dvir::isResolved()` — which checks `safetyStatus === 'resolved'` against the defect's data and is unlikely to behave as you expect. To use the typed defect helpers, instantiate `Defect` manually from the response array, or pull defects via `$dvir->vehicleDefects` / `$dvir->trailerDefects`.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Defect ID. |
| `defectType` | `?string` | Type of defect (free-form upstream). |
| `comment` | `?string` | Driver comment. |
| `isResolved` | `?bool` | Whether the defect has been resolved. |
| `createdAtTime` | `?string` | RFC 3339 creation timestamp. |
| `resolvedAtTime` | `?string` | RFC 3339 resolution timestamp. |
| `mechanicNotes` | `?string` | Mechanic notes. |
| `mechanicNotesUpdatedAtTime` | `?string` | RFC 3339 timestamp of the last mechanic-note edit. |
| `vehicle` | `?array{id?: string, name?: string}` | Vehicle the defect was logged against. |
| `trailer` | `?array{id?: string, name?: string}` | Trailer the defect was logged against. |
| `resolvedBy` | `?array{id?: string, name?: string}` | User who resolved the defect. |

To opt into typed helpers, wrap the array yourself:

```php
use Samsara\Data\Maintenance\Defect;

foreach (Samsara::maintenance()->defects()->get() as $row) {
    $defect = Defect::make($row->toArray());

    if ($defect->isResolved()) {
        // ...
    }
}
```

## Filtering

Refer to the [query builder](../query-builder.md) for the full operator reference. Both the DVIR and defects builders accept the standard filters:

```php
$dvirs = Samsara::maintenance()
    ->dvirs()
    ->whereDriver('driver-id')
    ->whereVehicle('vehicle-id')
    ->whereTag('fleet-west')
    ->between(now()->subDays(7), now())
    ->get();
```

The companion `MaintenanceStatus` enum (`CANCELLED`, `COMPLETED`, `IN_PROGRESS`, `OPEN`) maps the work-order status strings — see [Enums](../enums.md#maintenancestatus) for the full case-to-value mapping.

## Helper Methods

The `Dvir` entity exposes the following helpers:

```php
$dvir = Samsara::maintenance()->dvirs()->first();

$dvir->isPreTrip();             // bool — type === 'preTrip'
$dvir->isPostTrip();            // bool — type === 'postTrip'
$dvir->isMechanicInspection();  // bool — type === 'mechanic'
$dvir->isSafe();                // bool — safetyStatus === 'safe'
$dvir->isUnsafe();              // bool — safetyStatus === 'unsafe'
$dvir->isResolved();            // bool — safetyStatus === 'resolved'
```

## Properties

The `Dvir` entity exposes the following `@property-read` keys:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | DVIR ID. |
| `type` | `?string` | Inspection type (`preTrip`, `postTrip`, `mechanic`, `unspecified`). |
| `safetyStatus` | `?string` | Safety status (`safe`, `unsafe`, `resolved`). |
| `startTime` | `?string` | RFC 3339 start time. |
| `endTime` | `?string` | RFC 3339 end time. |
| `odometerMeters` | `?int` | Odometer reading in meters at inspection time. |
| `licensePlate` | `?string` | License plate captured at inspection time. |
| `mechanicNotes` | `?string` | Mechanic notes. |
| `trailerName` | `?string` | Trailer name captured at inspection time. |
| `vehicle` | `?array{id?: string, name?: string}` | Vehicle inspected. |
| `trailer` | `?array{id?: string, name?: string}` | Trailer inspected (single nested object). |
| `location` | `?array{latitude?: float, longitude?: float}` | Inspection location. |
| `authorSignature` | `?array{signedAtTime?: string}` | Driver signature payload. |
| `secondSignature` | `?array{signedAtTime?: string}` | Second-driver signature payload. |
| `thirdSignature` | `?array{signedAtTime?: string}` | Third-driver signature payload. |
| `vehicleDefects` | `?array<int, array{id?: string, defectType?: string}>` | Vehicle-side defects logged on the DVIR. |
| `trailerDefects` | `?array<int, array{id?: string, defectType?: string}>` | Trailer-side defects logged on the DVIR. |

> **Note:** The entity does not expose flat `driverId`, `vehicleId`, `trailerIds`, or `createdAtTime` keys. Read the driver from the upstream `dvir.driver` payload, the vehicle from `$dvir->vehicle['id']`, and the timestamps from `$dvir->startTime` / `$dvir->endTime`.

## Related Resources

- [Vehicles](vehicles.md) — resolve `$dvir->vehicle['id']`.
- [Trailers](trailers.md) — resolve `$dvir->trailer['id']`.
- [Drivers](drivers.md) — pair DVIRs with hours-of-service activity.
- [Work Orders](work-orders.md) — schedule shop work for unresolved defects.
- [Enums](../enums.md#maintenancestatus) — `MaintenanceStatus` reference.
- [Error Handling](../error-handling.md) — exceptions raised by the underlying request.

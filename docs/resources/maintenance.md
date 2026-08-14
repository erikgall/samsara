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

The `Samsara::maintenance()` resource exposes Driver Vehicle Inspection Reports (DVIRs) and the defects logged against them. DVIR builders return `Dvir`; the defects builder returns `Defect`.

`dvirs()` targets `/dvirs/stream`, `defects()` targets `/defects/stream`, and `createDvir()` posts to `/fleet/dvirs`. Dedicated item and mutation methods own their distinct contract paths. No inherited generic CRUD operation is supported by this multi-endpoint resource.

## Retrieving Records

Use `dvirs()` for the stream. Generic `all()` is rejected locally.

```php
use Samsara\Facades\Samsara;

$dvirs = Samsara::maintenance()
    ->dvirs()
    ->between(now()->subDays(7), now())
    ->get();

$recent = Samsara::maintenance()
    ->dvirs()
    ->startTime(now()->subHour())
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

Use the direct item helpers when you already have an ID:

```php
$dvir = Samsara::maintenance()->findDvir('dvir-id');
$defect = Samsara::maintenance()->findDefect('defect-id');
```

## Creating Records

`createDvir()` posts to `/fleet/dvirs` and returns a `Dvir`.

```php
$dvir = Samsara::maintenance()->createDvir([
    'authorId' => 'mechanic-user-id',
    'vehicleId' => 'vehicle-id',
    'type' => 'mechanic',
    'safetyStatus' => 'safe',
    'odometerMeters' => 184_523,
    'mechanicNotes' => 'Replaced the passenger-side headlight.',
]);
```

The current create operation requires `authorId`, `safetyStatus`, and `type`; only `mechanic` is accepted for `type`. Supply either `vehicleId` or `trailerId`.

## Updating Records

```php
$dvir = Samsara::maintenance()->resolveDvir('dvir-id', [
    'authorId' => 'mechanic-user-id',
    'isResolved' => true,
]);

$defect = Samsara::maintenance()->updateDefect('defect-id', [
    'isResolved' => true,
    'mechanicNotes' => 'Replaced the damaged component.',
]);
```

Generic `update()` remains unsupported because the resource spans separate DVIR and defect paths.

## Deleting Records

Generic `delete()` is unsupported and fails locally.

> **Note:** Treat DVIRs as append-only. Resolve safety issues by uploading a follow-up inspection, not by deleting the old one.

## Defects

Defects are exposed through the `defects()` builder, which targets `/defects/stream`:

```php
$defects = Samsara::maintenance()
    ->defects()
    ->between(now()->subDays(30), now())
    ->where('isResolved', false)
    ->get();
```

Each row is a typed `Defect`. `isResolved()` uses the boolean response field; `isSafe()` and `isUnsafe()` match the documented `defectSafetyStatus` values exactly.

`defectTypes()` returns a no-implicit-limit builder of typed `DefectType` rows from `/defect-types`.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Defect ID. |
| `defectTypeId` | `?string` | Defect type ID. |
| `defectSafetyStatus` | `?string` | `safe` or `unsafe`. |
| `comment` | `?string` | Driver comment. |
| `isResolved` | `?bool` | Whether the defect has been resolved. |
| `createdAtTime` | `?string` | RFC 3339 creation timestamp. |
| `updatedAtTime` | `?string` | RFC 3339 update timestamp. |
| `resolvedAtTime` | `?string` | RFC 3339 resolution timestamp. |
| `mechanicNotes` | `?string` | Mechanic notes. |
| `defectPhotos` | `?array<int, array<string, mixed>>` | Photos attached to the defect. |
| `dvirId` | `?string` | Associated DVIR ID. |
| `vehicle` | `?array{id?: string, name?: string}` | Vehicle the defect was logged against. |
| `trailer` | `?array{id?: string, name?: string}` | Trailer the defect was logged against. |
| `resolvedBy` | `?array{id?: string, name?: string}` | User who resolved the defect. |

Typed helpers are available directly:

```php
foreach (Samsara::maintenance()->defects()->startTime(now()->subDay())->get() as $defect) {
    if ($defect->isUnsafe() && ! $defect->isResolved()) {
        // ...
    }
}
```

Pass a `safetyStatus` array through `query()->where('safetyStatus', [...])` to emit repeated query keys, as required by the endpoint.

## Filtering

Refer to the [query builder](../query-builder.html) for the fluent operator reference, but use only parameters declared by the selected stream. DVIRs accept time and `safetyStatus` filters; defects accept time and `isResolved` filters.

```php
$dvirs = Samsara::maintenance()
    ->dvirs()
    ->where('safetyStatus', ['unsafe'])
    ->between(now()->subDays(7), now())
    ->get();
```

The package's `MaintenanceStatus` enum has four lowercase values. Current upstream work orders define eleven title-cased statuses, so the enum is not the current upstream wire contract. See [Enums](../enums.html#maintenancestatus).

## Helper Methods

The `Dvir` entity exposes the following helpers:

```php
$dvir = Samsara::maintenance()->dvirs()->startTime(now()->subDay())->first();

$dvir->isPreTrip();             // bool — type === 'preTrip'
$dvir->isPostTrip();            // bool — type === 'postTrip'
$dvir->isMechanicInspection();  // bool — type === 'mechanic'
$dvir->isSafe();                // bool — safetyStatus === 'safe'
$dvir->isUnsafe();              // bool — safetyStatus === 'unsafe'
$dvir->isResolved();            // bool — safetyStatus === 'resolved'
$dvir->hasDefects();            // bool — any documented defect collection is non-empty
```

`hasDefects()` handles both DVIR response variants: stream rows expose `defectIds`, while create responses can expose `vehicleDefects` and `trailerDefects`.

## Properties

The `Dvir` entity exposes the following `@property-read` keys:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | DVIR ID. |
| `type` | `?string` | Inspection type (`preTrip`, `postTrip`, `mechanic`, `unspecified`). |
| `safetyStatus` | `?string` | Safety status (`safe`, `unsafe`, `resolved`). |
| `defectIds` | `?array<int, string>` | Stream defect IDs. |
| `formattedAddress` | `?string` | Stream location address. |
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

- [Vehicles](vehicles.html) — resolve `$dvir->vehicle['id']`.
- [Trailers](trailers.html) — resolve `$dvir->trailer['id']`.
- [Drivers](drivers.html) — pair DVIRs with hours-of-service activity.
- [Work Orders](work-orders.html) — schedule shop work for unresolved defects.
- [Enums](../enums.html#maintenancestatus) — `MaintenanceStatus` reference.
- [Error Handling](../error-handling.html) — exceptions raised by the underlying request.

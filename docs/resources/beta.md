---
title: Beta Resources
nav_order: 90
description: Beta Samsara endpoints surfaced through the SDK.
permalink: /resources/beta
---

# Beta Resources

> **Warning:** Beta endpoints are subject to change without notice. Method signatures, response shapes, and even endpoint availability may evolve. Pin to a tagged release of the SDK if you depend on these methods.

- [Introduction](#introduction)
- [Method Reference](#method-reference)
  - [AEMP and Industrial Jobs](#aemp-and-industrial-jobs)
  - [Detections](#detections)
  - [Devices](#devices)
  - [HOS ELD Events](#hos-eld-events)
  - [Qualifications](#qualifications)
  - [Readings](#readings)
  - [Reports](#reports)
  - [Safety Scores](#safety-scores)
  - [Trailer Stats](#trailer-stats)
  - [Training](#training)
  - [Vehicle Immobilizer](#vehicle-immobilizer)
- [Related Resources](#related-resources)

## Introduction

`Samsara::beta()` returns a `BetaResource`. Despite the class name, its helpers mix GA and beta operations. All read helpers except `aempFleet()` return a `Builder`; the AEMP method preserves its raw wrapper and mutators return raw arrays or success booleans.

Current upstream staging and path review:

- Detections, devices, reports, and vehicle-immobilizer stream include beta operations.
- Readings definitions/history/latest, safety scores, qualifications, training, and trailer stats include GA operations.
- AEMP is now `/beta/aemp/Fleet/{pageNumber}`; industrial jobs use the `/beta/industrial/jobs` collection path; HOS ELD events use `/beta/fleet/hos/drivers/eld-events`.
- Qualifications use `/qualification-records` and `/qualification-types`; training uses `/training-assignments`, `/training-assignments/stream`, and `/training-courses`.

The method reference below records implemented package behavior. Additive official operations not listed here remain deferred.

## Method Reference

### AEMP and Industrial Jobs

#### `aempFleet(string $pageNumber): array`

Calls `GET /beta/aemp/Fleet/{pageNumber}` and preserves the raw AEMP Fleet wrapper.

```php
use Samsara\Facades\Samsara;

$fleet = Samsara::beta()->aempFleet('1');
```

#### `industrialJobs(): Builder`

Returns a no-implicit-limit query builder for `/beta/industrial/jobs`.

```php
$jobs = Samsara::beta()->industrialJobs()->get();
```

#### `createIndustrialJob(array $data): array`

`POST /beta/industrial/jobs`. The request body must contain a nested `job` payload. Returns the decoded `data` payload.

```php
$job = Samsara::beta()->createIndustrialJob(['job' => [
    'id' => '8d218e6c-7a16-4f9f-90f7-cc1d93b9e596',
    'name' => 'Crane lift — bay 4',
    'industrialAssetIds' => ['industrial-asset-id'],
    'startDate' => '2026-01-15T08:00:00Z',
    'endDate' => '2026-01-15T16:00:00Z',
]]);
```

#### `updateIndustrialJob(string $id, array $data): array`

`PATCH /beta/industrial/jobs?id={id}`. Supply the nested `job` payload. Returns the decoded `data` payload.

```php
$job = Samsara::beta()->updateIndustrialJob('job-id', ['job' => [
    'name' => 'Crane lift — bay 4 (rescheduled)',
]]);
```

#### `deleteIndustrialJob(string $id): array`

`DELETE /beta/industrial/jobs?id={id}`. Returns the decoded response array.

```php
$deleted = Samsara::beta()->deleteIndustrialJob('job-id');
```

### Detections

#### `detectionsStream(): Builder`

Returns a query builder for `/detections/stream`, the live detections feed.

```php
$detections = Samsara::beta()->detectionsStream()->startTime($start)->lazy();
```

### Devices

#### `devices(): Builder`

Returns a builder for `GET /devices`.

```php
$devices = Samsara::beta()->devices()->get();

foreach ($devices as $device) {
    logger()->info($device['serial']);
}
```

### HOS ELD Events

#### `hosEldEvents(): Builder`

Returns a query builder for `/beta/fleet/hos/drivers/eld-events` with the operation's default limit of 25.

```php
$events = Samsara::beta()
    ->hosEldEvents()
    ->between($start, $end)
    ->whereDriver('driver-id')
    ->get();
```

### Qualifications

#### `qualificationsRecords(): Builder`

Returns a query builder for `/qualification-records`.

```php
$records = Samsara::beta()
    ->qualificationsRecords()
    ->repeatQueryParameters('ids')
    ->where('ids', ['qualification-id'])
    ->get();
```

`ids` is required and uses repeated query keys. The explicit `repeatQueryParameters()` call is required until the resource helper declares that encoding itself.

#### `qualificationsTypes(): Builder`

Returns a no-implicit-limit builder for `GET /qualification-types`.

```php
$types = Samsara::beta()
    ->qualificationsTypes()
    ->where('entityType', 'worker')
    ->get();
```

### Readings

#### `readingsDefinitions(): Builder`

Returns a no-implicit-limit builder for `GET /readings/definitions`.

```php
$definitions = Samsara::beta()->readingsDefinitions()->get();
```

#### `readingsHistory(): Builder`

Returns a query builder for `/readings/history`.

```php
$history = Samsara::beta()
    ->readingsHistory()
    ->where('readingId', 'reading-id')
    ->where('entityType', 'asset')
    ->between($start, $end)
    ->get();
```

#### `readingsLatest(): Builder`

Returns a query builder for `/readings/latest`.

```php
$latest = Samsara::beta()
    ->readingsLatest()
    ->where('readingIds', 'reading-id')
    ->where('entityType', 'asset')
    ->get();
```

#### `createReading(array $data): bool`

`POST /readings`. The official request requires a top-level `data` envelope; the helper returns whether the empty-body response was successful.

`$readingValue` must match the selected reading definition's value object from `/readings/definitions`.

```php
$created = Samsara::beta()->createReading(['data' => [[
    'entityType' => 'asset',
    'entityId' => 'asset-id',
    'readingId' => 'reading-id',
    'happenedAtTime' => '2026-01-15T14:30:00Z',
    'value' => $readingValue,
]]]);
```

### Reports

#### `reportsConfigs(): Builder`

Returns a builder for `GET /reports/configs`.

```php
$configs = Samsara::beta()->reportsConfigs()->get();
```

#### `reportsDatasets(): Builder`

Returns a query builder for `/reports/datasets`.

```php
$datasets = Samsara::beta()->reportsDatasets()->get();
```

#### `reportsRuns(): Builder`

Returns a query builder for `/reports/runs`.

```php
$runs = Samsara::beta()->reportsRuns()->get();
```

### Safety Scores

#### `safetyScoresDrivers(): Builder`

Returns a query builder for `/safety-scores/drivers`.

```php
$scores = Samsara::beta()
    ->safetyScoresDrivers()
    ->between($start, $end)
    ->get();
```

#### `safetyScoresVehicles(): Builder`

Returns a query builder for `/safety-scores/vehicles`.

```php
$scores = Samsara::beta()
    ->safetyScoresVehicles()
    ->between($start, $end)
    ->get();
```

### Trailer Stats

#### `trailerStatsCurrent(): Builder`

Returns a query builder for `/fleet/trailers/stats`, the latest snapshot per trailer.

```php
$stats = Samsara::beta()->trailerStatsCurrent()->types(['gps'])->get();
```

#### `trailerStatsFeed(): Builder`

Returns a query builder for `/fleet/trailers/stats/feed`, the streaming feed.

```php
$feed = Samsara::beta()->trailerStatsFeed()->types(['gps'])->lazy();
```

#### `trailerStatsHistory(): Builder`

Returns a query builder for `/fleet/trailers/stats/history`.

```php
$history = Samsara::beta()
    ->trailerStatsHistory()
    ->types(['gps'])
    ->between($start, $end)
    ->get();
```

### Training

#### `trainingAssignments(): Builder`

Returns a no-implicit-limit query builder for `/training-assignments/stream`.

```php
$assignments = Samsara::beta()
    ->trainingAssignments()
    ->startTime($start)
    ->get();
```

#### `trainingCourses(): Builder`

Returns a no-implicit-limit builder for `GET /training-courses`.

```php
$courses = Samsara::beta()->trainingCourses()->get();
```

### Vehicle Immobilizer

#### `vehicleImmobilizerStream(): Builder`

Returns a query builder for `/fleet/vehicles/immobilizer/stream`, the lock/unlock state feed. Preview control operations remain contract-stage and unsuitable for business-critical use.

```php
$stream = Samsara::beta()
    ->vehicleImmobilizerStream()
    ->whereVehicle('vehicle-id')
    ->startTime($start)
    ->lazy();
```

## Related Resources

- [Resources Index](index.html) — full list of stable resources.
- [Preview Resources](preview.html) — operational lock and unlock commands paired with `vehicleImmobilizerStream()`.
- [Legacy Resources](legacy.html) — v1 endpoints that still proxy through the SDK.
- [Query Builder](../query-builder.html) — filtering, pagination, and lazy iteration over the `Builder` returns above.

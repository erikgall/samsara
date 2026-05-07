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

Beta resources expose Samsara API endpoints that are still under development. Reach them through `Samsara::beta()`, which returns a `BetaResource` instance. Methods that return a `Builder` plug into the standard [query builder](../query-builder.md) for filtering, time bounds, and pagination. Methods that return an `EntityCollection` issue an immediate request and return a flat collection of generic `Entity` instances. The mutating helpers (`createIndustrialJob`, `updateIndustrialJob`, `deleteIndustrialJob`, `createReading`) call the underlying HTTP client directly and return the raw decoded JSON.

## Method Reference

### AEMP and Industrial Jobs

#### `aempFleet(): Builder`

Returns a query builder for the `/aemp/fleet` endpoint, the AEMP fleet feed.

```php
use Samsara\Facades\Samsara;

$fleet = Samsara::beta()->aempFleet()->get();
```

#### `industrialJobs(): Builder`

Returns a query builder for `/industrial/jobs`.

```php
$jobs = Samsara::beta()->industrialJobs()->whereTag('crane-fleet')->get();
```

#### `createIndustrialJob(array $data): array`

`POST /industrial/jobs`. Returns the decoded `data` payload.

```php
$job = Samsara::beta()->createIndustrialJob([
    'name' => 'Crane lift — bay 4',
    'machineId' => 'machine-id',
]);
```

#### `updateIndustrialJob(string $id, array $data): array`

`PATCH /industrial/jobs/{id}`. Returns the decoded `data` payload.

```php
$job = Samsara::beta()->updateIndustrialJob('job-id', [
    'name' => 'Crane lift — bay 4 (rescheduled)',
]);
```

#### `deleteIndustrialJob(string $id): bool`

`DELETE /industrial/jobs/{id}`. Returns `true` when the API responds with a successful status code.

```php
$deleted = Samsara::beta()->deleteIndustrialJob('job-id');
```

### Detections

#### `detectionsStream(): Builder`

Returns a query builder for `/detections/stream`, the live detections feed.

```php
$detections = Samsara::beta()->detectionsStream()->lazy();
```

### Devices

#### `devices(): EntityCollection`

`GET /devices`. Returns an `EntityCollection` of generic `Entity` instances.

```php
$devices = Samsara::beta()->devices();

foreach ($devices as $device) {
    logger()->info($device['serial']);
}
```

### HOS ELD Events

#### `hosEldEvents(): Builder`

Returns a query builder for `/fleet/hos/eld-events`.

```php
$events = Samsara::beta()
    ->hosEldEvents()
    ->between($start, $end)
    ->whereDriver('driver-id')
    ->get();
```

### Qualifications

#### `qualificationsRecords(): Builder`

Returns a query builder for `/qualifications/records`.

```php
$records = Samsara::beta()->qualificationsRecords()->get();
```

#### `qualificationsTypes(): EntityCollection`

`GET /qualifications/types`. Returns an `EntityCollection` of generic `Entity` instances.

```php
$types = Samsara::beta()->qualificationsTypes();
```

### Readings

#### `readingsDefinitions(): EntityCollection`

`GET /readings/definitions`. Returns an `EntityCollection` of generic `Entity` instances.

```php
$definitions = Samsara::beta()->readingsDefinitions();
```

#### `readingsHistory(): Builder`

Returns a query builder for `/readings/history`.

```php
$history = Samsara::beta()
    ->readingsHistory()
    ->between($start, $end)
    ->get();
```

#### `readingsLatest(): Builder`

Returns a query builder for `/readings/latest`.

```php
$latest = Samsara::beta()->readingsLatest()->get();
```

#### `createReading(array $data): array`

`POST /readings`. Returns the decoded `data` payload.

```php
$reading = Samsara::beta()->createReading([
    'definitionId' => 'definition-id',
    'value' => 42,
]);
```

### Reports

#### `reportsConfigs(): EntityCollection`

`GET /reports/configs`. Returns an `EntityCollection` of generic `Entity` instances.

```php
$configs = Samsara::beta()->reportsConfigs();
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
$stats = Samsara::beta()->trailerStatsCurrent()->get();
```

#### `trailerStatsFeed(): Builder`

Returns a query builder for `/fleet/trailers/stats/feed`, the streaming feed.

```php
$feed = Samsara::beta()->trailerStatsFeed()->lazy();
```

#### `trailerStatsHistory(): Builder`

Returns a query builder for `/fleet/trailers/stats/history`.

```php
$history = Samsara::beta()
    ->trailerStatsHistory()
    ->between($start, $end)
    ->get();
```

### Training

#### `trainingAssignments(): Builder`

Returns a query builder for `/training/assignments`.

```php
$assignments = Samsara::beta()->trainingAssignments()->get();
```

#### `trainingCourses(): EntityCollection`

`GET /training/courses`. Returns an `EntityCollection` of generic `Entity` instances.

```php
$courses = Samsara::beta()->trainingCourses();
```

### Vehicle Immobilizer

#### `vehicleImmobilizerStream(): Builder`

Returns a query builder for `/fleet/vehicles/immobilizer/stream`, the lock/unlock state feed. Pair it with the operational [Preview](preview.md) `lockVehicle()` and `unlockVehicle()` calls.

```php
$stream = Samsara::beta()->vehicleImmobilizerStream()->lazy();
```

## Related Resources

- [Resources Index](index.md) — full list of stable resources.
- [Preview Resources](preview.md) — operational lock and unlock commands paired with `vehicleImmobilizerStream()`.
- [Legacy Resources](legacy.md) — v1 endpoints that still proxy through the SDK.
- [Query Builder](../query-builder.md) — filtering, pagination, and lazy iteration over the `Builder` returns above.

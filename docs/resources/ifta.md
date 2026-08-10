---
title: IFTA
nav_order: 24
description: International Fuel Tax Agreement reports and CSV exports.
permalink: /resources/ifta
---

# IFTA

- [Introduction](#introduction)
- [Jurisdiction and Vehicle Reports](#jurisdiction-and-vehicle-reports)
- [CSV Exports](#csv-exports)
- [Method Reference](#method-reference)
- [Related Resources](#related-resources)

## Introduction

The four explicit methods on `IftaResource` return raw decoded JSON (`array<string, mixed>`); there is no typed entity, query builder, or supported generic CRUD operation.

The helpers implement `POST /ifta-detail/csv`, `GET /ifta-detail/csv/{id}`, `GET /fleet/reports/ifta/jurisdiction`, and `GET /fleet/reports/ifta/vehicle`.

## Jurisdiction and Vehicle Reports

Both reports take an inline `$params` array and return the API payload as an associative array.

```php
use Samsara\Facades\Samsara;

$jurisdiction = Samsara::ifta()->jurisdictionReport([
    'year' => 2026,
    'quarter' => 1,
]);

$vehicle = Samsara::ifta()->vehicleReport([
    'year' => 2026,
    'quarter' => 1,
    'vehicleIds' => ['vehicle-id'],
]);
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `year` | `int` | Reporting year. |
| `quarter` | `int` | Reporting quarter (1–4). |
| `tagIds` | `array<string>` | Filter to vehicles in the supplied tags. |
| `vehicleIds` | `array<string>` | (Vehicle report only) limit to specific vehicles. |

## CSV Exports

CSV exports use a request-then-poll pattern: `detailCsv()` queues an export and returns a job descriptor; `getDetailCsv()` retrieves the job (and a download URL once Samsara has rendered it).

```php
$export = Samsara::ifta()->detailCsv([
    'startHour' => '2026-01-01T00:00:00Z',
    'endHour' => '2026-04-01T00:00:00Z',
]);

$status = Samsara::ifta()->getDetailCsv($export['id']);

if ($status['status'] === 'completed') {
    Storage::put('ifta/q1.csv', file_get_contents($status['downloadUrl']));
}
```

Poll `getDetailCsv()` on a queued job (`Bus::dispatch(...)`); do not block the request thread.

## Method Reference

| Method | Returns | Description |
|--------|---------|-------------|
| `jurisdictionReport(array $params)` | `array` | Miles and gallons aggregated by jurisdiction. |
| `vehicleReport(array $params)` | `array` | Miles and gallons aggregated by vehicle. |
| `detailCsv(array $params)` | `array` | Queues a detail CSV export. |
| `getDetailCsv(string $id)` | `array` | Fetches an export job by ID. |

## Related Resources

- [Fuel and Energy](fuel-and-energy.html) — fuel volume and consumption metrics.
- [Trips](trips.html) — per-trip mileage that feeds IFTA totals.
- [Error Handling](../error-handling.html) — exception types raised on failed requests.

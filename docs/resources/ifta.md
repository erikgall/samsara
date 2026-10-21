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

The four raw methods on `IftaResource` return decoded JSON (`array<string, mixed>`). `vehicleReportQuery()` provides fluent cursor traversal for vehicle rows. There is no domain-specific entity or supported generic CRUD operation.

The helpers implement `POST /ifta-detail/csv`, `GET /ifta-detail/csv/{id}`, `GET /fleet/reports/ifta/jurisdiction`, and `GET /fleet/reports/ifta/vehicle`.

## Jurisdiction and Vehicle Reports

Both reports take an inline `$params` array and return the API payload as an associative array.

```php
use Samsara\Facades\Samsara;

$jurisdiction = Samsara::ifta()->jurisdictionReport([
    'year' => 2026,
    'quarter' => 'Q1',
    'tagIds' => 'tag-1,tag-2',
]);

$vehicle = Samsara::ifta()->vehicleReport([
    'year' => 2026,
    'quarter' => 'Q1',
    'vehicleIds' => 'vehicle-1,vehicle-2',
]);
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `year` | `int` | Reporting year. |
| `quarter` | `string` | Reporting quarter (`Q1`–`Q4`); mutually exclusive with `month`. |
| `tagIds` | comma-separated `string` | Filter to vehicles in the supplied tags. |
| `vehicleIds` | comma-separated `string` | Limit to specific vehicles or external IDs. |

These raw helpers forward `$params` unchanged. Join multi-value filters before calling them; passing PHP arrays would produce bracket encoding, not the required comma-separated query value.

Use the vehicle-report builder when you want only the paginated rows:

```php
$vehicles = Samsara::ifta()
    ->vehicleReportQuery()
    ->where('year', 2026)
    ->where('quarter', 'Q1')
    ->paginate();
```

It maps `data.vehicleReports` to generic `Entity` objects without adding an unsupported `limit`. Keep using `vehicleReport()` when you also need page-level `year`, `month`, `quarter`, or troubleshooting data.

## CSV Exports

CSV exports use a request-then-poll pattern: `detailCsv()` queues an export and returns a job descriptor; `getDetailCsv()` retrieves that job. Poll with `jobId` until `jobStatus` is `Completed` or `Failed`. A completed job exposes one or more files, each with its own `downloadUrl`.

```php
$export = Samsara::ifta()->detailCsv([
    'startHour' => '2026-01-01T00:00:00Z',
    'endHour' => '2026-02-01T00:00:00Z',
]);

$status = Samsara::ifta()->getDetailCsv($export['jobId']);

if ($status['jobStatus'] === 'Completed') {
    foreach ($status['files'] ?? [] as $file) {
        Storage::put(
            'ifta/'.$file['name'],
            file_get_contents($file['downloadUrl']),
        );
    }
}
```

Status values are case-sensitive: `Requested`, `Processing`, `Completed`, or `Failed`. There is no current top-level `id`, `status`, or `downloadUrl`. Poll `getDetailCsv()` on a queued job (`Bus::dispatch(...)`); do not block the request thread.

Each detail job may span at most one month. Split a quarterly or longer export into non-overlapping one-month-or-shorter jobs and combine their completed output files in application code.

## Method Reference

| Method | Returns | Description |
|--------|---------|-------------|
| `jurisdictionReport(array $params)` | `array` | Miles and gallons aggregated by jurisdiction. |
| `vehicleReport(array $params)` | `array` | Miles and gallons aggregated by vehicle. |
| `vehicleReportQuery()` | `Builder` | Fluent cursor traversal over `data.vehicleReports`. |
| `detailCsv(array $params)` | `array` | Queues a detail CSV export. |
| `getDetailCsv(string $id)` | `array` | Fetches an export job by `jobId`. |

## Related Resources

- [Fuel and Energy](fuel-and-energy.html) — fuel volume and consumption metrics.
- [Trips](trips.html) — per-trip mileage that feeds IFTA totals.
- [Error Handling](../error-handling.html) — exception types raised on failed requests.

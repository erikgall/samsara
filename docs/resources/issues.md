---
title: Issues
nav_order: 31
description: Vehicle and equipment fault and issue stream.
permalink: /resources/issues
---

# Issues

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::issues()` resource exposes the `/issues/stream` feed of vehicle and equipment fault data — diagnostic trouble codes, low-battery flags, and similar maintenance signals. It is read-only and returns generic `Entity` instances, since `IssuesResource` does not declare a typed `$entity`.

## Retrieving Records

Issues are streamed through the `stream()` builder. The base CRUD methods are inherited but the upstream endpoint does not honour them — treat the resource as builder-only.

```php
use Samsara\Facades\Samsara;

$issues = Samsara::issues()
    ->stream()
    ->between(now()->subDays(30), now())
    ->get();

foreach ($issues as $issue) {
    Issue::record($issue->toArray());
}
```

For long windows, prefer the `lazy()` cursor:

```php
Samsara::issues()
    ->stream()
    ->between(now()->subYear(), now())
    ->lazy(500)
    ->each(fn ($issue) => Issue::record($issue->toArray()));
```

## Filtering

The full operator list lives on the [query builder](../query-builder.md) page. The issues stream commonly accepts:

```php
$issues = Samsara::issues()
    ->stream()
    ->whereVehicle('vehicle-id')
    ->whereTag('maintenance-required')
    ->between(now()->subDays(30), now())
    ->limit(100)
    ->get();
```

A `between()` window is required by the upstream endpoint.

## Properties

`IssuesResource` does not declare a typed `$entity`, so each result is a generic `Samsara\Data\Entity` (a `Fluent` instance). The keys returned by the API are typically:

| Key | Type | Description |
|-----|------|-------------|
| `id` | `string` | Issue ID. |
| `vehicle` | `array{id?: string, name?: string}` | Vehicle that produced the issue. |
| `firstOccurredAtTime` | `string` | RFC 3339 timestamp of the first occurrence. |
| `lastOccurredAtTime` | `string` | RFC 3339 timestamp of the most recent occurrence. |
| `description` | `string` | Issue description. |

Read keys with array access (`$issue['description']`) or magic-property access (`$issue->description`). No helper methods are available, since no typed entity is bound.

## Related Resources

- [Maintenance](maintenance.md) — DVIRs and defect tracking.
- [Work Orders](work-orders.md) — translate issues into scheduled work.
- [Alerts](alerts.md) — notify on issue creation.
- [Error Handling](../error-handling.md) — exceptions raised by the underlying request.

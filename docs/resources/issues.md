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

The `Samsara::issues()` resource exposes the `/issues/stream` feed of vehicle and equipment fault data. Stream rows are generic `Entity` instances. The only supported generic mutation is `create()` on the resource's configured endpoint; `all`, `find`, `update`, and `delete` are rejected.

## Retrieving Records

Read issues through the `stream()` builder. Use `create()` only for the package-owned create operation.

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

The full operator list lives on the [query builder](../query-builder.html) page. The issues stream commonly accepts:

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

- [Maintenance](maintenance.html) — DVIRs and defect tracking.
- [Work Orders](work-orders.html) — translate issues into scheduled work.
- [Alerts](alerts.html) — notify on issue creation.
- [Error Handling](../error-handling.html) — exceptions raised by the underlying request.

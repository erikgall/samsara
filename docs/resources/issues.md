---
title: Issues
nav_order: 31
description: Vehicle and equipment fault and issue stream.
permalink: /resources/issues
---

# Issues

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Updating Records](#updating-records)
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::issues()` resource exposes the `/issues/stream` feed of vehicle and equipment fault data. Stream rows are generic `Entity` instances. Generic `create()` and the dedicated collection-level `updateIssue()` method are supported; `all`, `find`, `update`, and `delete` are rejected because their inherited item paths do not match this API.

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
    ->lazy()
    ->each(fn ($issue) => Issue::record($issue->toArray()));
```

## Updating Records

`updateIssue()` sends the issue ID inside the collection-level PATCH body and returns the updated generic `Entity`.

```php
$issue = Samsara::issues()->updateIssue([
    'id' => 'issue-id',
    'status' => 'resolved',
]);
```

## Filtering

The full operator list lives on the [query builder](../query-builder.html) page. The issues stream commonly accepts:

```php
$issues = Samsara::issues()
    ->stream()
    ->where('assetIds', 'vehicle-id')
    ->where('status', 'open')
    ->between(now()->subDays(30), now())
    ->get();
```

`startTime` is required by the upstream endpoint; `endTime` is optional. `between()` supplies both.

## Properties

`IssuesResource` does not declare a typed `$entity`, so each result is a generic `Samsara\Data\Entity` (a `Fluent` instance). The keys returned by the API are typically:

| Key | Type | Description |
|-----|------|-------------|
| `id` | `string` | Issue ID. |
| `asset` | `?array` | Asset associated with the issue, when present. |
| `assignedTo` | `?array` | Assigned user details, when present. |
| `createdAtTime` | `string` | RFC 3339 creation time. |
| `description` | `?string` | Issue description. |
| `dueDate` | `?string` | Due date, when present. |
| `externalIds` | `?array<string, string>` | External identifier map. |
| `issueSource` | `array` | Issue-origin details. |
| `mediaList` | `?array` | Attached media. |
| `priority` | `?string` | Issue priority. |
| `status` | `string` | Issue status. |
| `submittedAtTime` | `string` | RFC 3339 submission time. |
| `submittedBy` | `array` | Submitting user details. |
| `title` | `string` | Issue title. |
| `updatedAtTime` | `string` | RFC 3339 last-update time. |

Read keys with array access (`$issue['description']`) or magic-property access (`$issue->description`). No helper methods are available, since no typed entity is bound.

## Related Resources

- [Maintenance](maintenance.html) — DVIRs and defect tracking.
- [Work Orders](work-orders.html) — translate issues into scheduled work.
- [Alerts](alerts.html) — notify on issue creation.
- [Error Handling](../error-handling.html) — exceptions raised by the underlying request.

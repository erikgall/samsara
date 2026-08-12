---
title: Forms
nav_order: 23
description: Manage form submissions and access form templates.
permalink: /resources/forms
---

# Forms

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
  - [Form Submissions](#form-submissions)
  - [Form Templates](#form-templates)
  - [Form Submissions Stream](#form-submissions-stream)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [PDF Exports](#pdf-exports)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Forms expose the inspections, checklists, and reports your drivers fill out from the Samsara app. `submissions()`, `submissionsStream()`, and `templates()` all return builders. Submission builders map `FormSubmission`; template builders map `FormTemplate`.

The resource owns only generic `create`; use `updateSubmission()` for a collection-path update. PDF start and poll both use `/form-submissions/pdf-exports`, with `id` and `pdfId` query parameters respectively.

## Retrieving Records

### Form Submissions

```php
use Samsara\Facades\Samsara;

$submissions = Samsara::forms()->submissions()->get();
```

`submissions()` returns a query builder, so you can chain `->whereDriver()`, `->between()`, and the rest of the standard filters before calling `->get()`.

### Form Templates

`templates()` returns a no-implicit-limit builder of `FormTemplate` objects.

```php
$templates = Samsara::forms()->templates()->get();
```

### Form Submissions Stream

For change-feed style polling, use the stream builder. `between()` is required.

```php
use Carbon\Carbon;

$submissions = Samsara::forms()
    ->submissionsStream()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();
```

## Creating Records

```php
$submission = Samsara::forms()->createSubmission([
    'formTemplateId' => 'template-123',
    'driverId'       => 'driver-456',
    'fields' => [
        ['id' => 'field-1', 'value' => 'Response text'],
    ],
]);
```

## Updating Records

```php
$submission = Samsara::forms()->updateSubmission('submission-id', [
    'fields' => [
        ['id' => 'field-1', 'value' => 'Updated response'],
    ],
]);
```

## Deleting Records

Generic `delete()` is unsupported and throws before HTTP.

## Filtering

Submissions accept the standard query builder. See [the query builder reference](../query-builder.html) for the full method list.

```php
$submissions = Samsara::forms()
    ->submissions()
    ->whereDriver('driver-123')
    ->whereTag('inspection-forms')
    ->get();
```

You can also call `Samsara::forms()->query()` directly for the same builder if you prefer the generic accessor.

## PDF Exports

PDF generation follows the **request-then-poll** pattern (the same pattern used by [camera media](camera-media.html)). You submit an export request and receive an export ID, then poll `getPdfExport()` until the file is ready.

```php
$export = Samsara::forms()->exportPdf('submission-id');

$exportId = $export['id'];

$pdf = Samsara::forms()->getPdfExport($exportId);
```

Both methods return raw `array<string, mixed>` payloads from the API.

## Helper Methods

The `FormSubmission` entity exposes status helpers:

```php
$submission = Samsara::forms()->submissions()->where('ids', ['submission-id'])->first();

$submission->isNotStarted();       // bool
$submission->isCompleted();        // bool
$submission->isArchived();         // bool
$submission->isInProgress();       // bool
$submission->needsReview();        // bool
$submission->hasChangesRequested(); // bool
$submission->isApproved();         // bool
```

Each method matches one documented wire value exactly. Missing, null, empty, and unknown statuses return `false`. The older `isDraft()` and `isSubmitted()` methods remain callable for compatibility but are deprecated because `draft` and `submitted` are not current response statuses.

## Properties

The `FormSubmission` entity (`Samsara\Data\Form\FormSubmission`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Form submission ID. |
| `formTemplate` | `?array<string, mixed>` | Form template reference. |
| `status` | `?string` | `notStarted`, `completed`, `archived`, `inProgress`, `needsReview`, `changesRequested`, or `approved`. |
| `title` | `?string` | Submission title. |
| `approvalDetails` | `?array<string, mixed>` | Approval workflow details. |
| `asset` | `?array<string, mixed>` | Associated asset. |
| `assignedTo` | `?array<string, mixed>` | Assigned user or driver. |
| `assignedAtTime` | `?string` | Assignment timestamp (RFC 3339). |
| `dueAtTime` | `?string` | Due timestamp (RFC 3339). |
| `durationMs` | `?int` | Submission duration in milliseconds. |
| `externalIds` | `?array<string, string>` | External ID mappings. |
| `fields` | `?array<int, array<string, mixed>>` | Submitted form fields. |
| `geofence` | `?array<string, mixed>` | Submission geofence. |
| `isRequired` | `?bool` | Whether completion is required. |
| `location` | `?array<string, mixed>` | Submission location. |
| `routeId` | `?string` | Related route ID. |
| `routeStopId` | `?string` | Related route-stop ID. |
| `score` | `?float` | Submission score when present. |
| `submittedBy` | `?array<string, mixed>` | User or driver who submitted the form. |
| `createdAtTime` | `?string` | Creation timestamp (RFC 3339). |
| `updatedAtTime` | `?string` | Last update timestamp (RFC 3339). |
| `submittedAtTime` | `?string` | Submission timestamp (RFC 3339). |

`templates()->get()` returns `Samsara\Data\Form\FormTemplate` entities with these properties:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Template ID. |
| `title` | `?string` | Template title. |
| `description` | `?string` | Template description. |
| `revisionId` | `?string` | Template revision ID. |
| `formCategory` | `?string` | Template category. |
| `fields` | `?array<int, array<string, mixed>>` | Field definitions. |
| `sections` | `?array<int, array<string, mixed>>` | Template sections. |
| `approvalConfig` | `?array<string, mixed>` | Approval configuration. |
| `createdBy` | `?array<string, mixed>` | Template creator. |
| `updatedBy` | `?array<string, mixed>` | Last updater. |
| `createdAtTime` | `?string` | Creation timestamp (RFC 3339). |
| `updatedAtTime` | `?string` | Last update timestamp (RFC 3339). |

## Related Resources

- [Drivers](drivers.html) — driver records referenced from submissions.
- [Vehicles](vehicles.html) — vehicle records referenced from submissions.
- [Camera Media](camera-media.html) — same request-then-poll pattern for video exports.
- [Query Builder](../query-builder.html) — filters and pagination.

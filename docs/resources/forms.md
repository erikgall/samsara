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

$submissions = Samsara::forms()
    ->submissions()
    ->where('ids', 'submission-id-1,submission-id-2')
    ->get();
```

The current endpoint requires `ids`: pass one ID or a comma-separated list of up to 100 IDs. Its only other filter is the optional comma-separated `include` value (`externalIds`). It does not support the generic driver, tag, or time-window helpers.

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
    'formTemplate' => [
        'id' => '9814a1fa-f0c6-408b-bf85-51dc3bc71ac7',
    ],
    'status' => 'notStarted',
    'assignedTo' => [
        'id' => 'driver-456',
        'type' => 'driver',
    ],
    'fields' => [
        [
            'id' => '9814a1fa-f0c6-408b-bf85-51dc3bc71ac8',
            'type' => 'text',
            'textValue' => ['value' => 'Response text'],
        ],
    ],
]);
```

`formTemplate` and `status` are required. The only accepted create status is `notStarted`. If supplied, `assignedTo` requires both `id` and `type` (`driver` or `user`), and every field requires `id`, `type`, and the value object that matches that type, such as `textValue` for `text`.

## Updating Records

```php
$submission = Samsara::forms()->updateSubmission('submission-id', [
    'status' => 'inProgress',
    'fields' => [
        [
            'id' => '9814a1fa-f0c6-408b-bf85-51dc3bc71ac8',
            'type' => 'text',
            'textValue' => ['value' => 'Updated response'],
        ],
    ],
]);
```

The helper adds the submission `id` to the collection PATCH body. Do not put the ID in an item path.

## Deleting Records

Generic `delete()` is unsupported and throws before HTTP.

## Filtering

The direct submissions endpoint accepts only required `ids` and optional `include`, both as comma-separated strings. Use `submissionsStream()` for time-bounded change-feed reads.

```php
$submissions = Samsara::forms()
    ->submissions()
    ->where('ids', 'submission-id-1,submission-id-2')
    ->where('include', 'externalIds')
    ->get();
```

## PDF Exports

PDF generation follows the **request-then-poll** pattern (the same pattern used by [camera media](camera-media.html)). Submit the form-submission ID, then poll with the returned `pdfId` until `jobStatus` is `done` or `failed`.

```php
$export = Samsara::forms()->exportPdf('submission-id');

$pdfId = $export['pdfId'];

$pdf = Samsara::forms()->getPdfExport($pdfId);

if ($pdf['jobStatus'] === 'done') {
    $downloadUrl = $pdf['pdfUrl'];
}
```

Both methods return raw `array<string, mixed>` payloads from the API. `id` is the form-submission ID; it is not the polling identifier. Current response keys are `pdfId`, `jobStatus`, and, when complete, `pdfUrl`. The legacy `status` and `url` keys are not returned.

## Helper Methods

The `FormSubmission` entity exposes status helpers:

```php
$submission = Samsara::forms()->submissions()->where('ids', 'submission-id')->first();

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

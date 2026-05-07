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

Forms expose the inspections, checklists, and reports your drivers fill out from the Samsara app. The resource has two distinct shapes: `submissions()` returns a query builder over completed `FormSubmission` records, while `templates()` returns the template definitions immediately as an `EntityCollection` (no builder, no `->get()`). Reach for forms when you need to ingest driver-submitted data, generate PDFs of submissions, or inspect the templates available to your fleet.

## Retrieving Records

### Form Submissions

```php
use Samsara\Facades\Samsara;

$submissions = Samsara::forms()->submissions()->get();
```

`submissions()` returns a query builder, so you can chain `->whereDriver()`, `->between()`, and the rest of the standard filters before calling `->get()`.

### Form Templates

`templates()` is a direct accessor — it issues the request and returns an `EntityCollection<FormTemplate>` immediately.

```php
$templates = Samsara::forms()->templates();
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

> **Note:** The Samsara API does not support deleting form submissions through this resource.

## Filtering

Submissions accept the standard query builder. See [the query builder reference](../query-builder.md) for the full method list.

```php
$submissions = Samsara::forms()
    ->submissions()
    ->whereDriver('driver-123')
    ->whereTag('inspection-forms')
    ->get();
```

You can also call `Samsara::forms()->query()` directly for the same builder if you prefer the generic accessor.

## PDF Exports

PDF generation follows the **request-then-poll** pattern (the same pattern used by [camera media](camera-media.md)). You submit an export request and receive an export ID, then poll `getPdfExport()` until the file is ready.

```php
$export = Samsara::forms()->exportPdf([
    'formSubmissionIds' => ['submission-1', 'submission-2'],
]);

$exportId = $export['id'];

$pdf = Samsara::forms()->getPdfExport($exportId);
```

Both methods return raw `array<string, mixed>` payloads from the API.

## Helper Methods

The `FormSubmission` entity exposes status helpers:

```php
$submission = Samsara::forms()->submissions()->find('submission-id');

$submission->isDraft();     // bool
$submission->isSubmitted(); // bool
```

## Properties

The `FormSubmission` entity (`Samsara\Data\Form\FormSubmission`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Form submission ID. |
| `formTemplateId` | `?string` | Form template ID. |
| `status` | `?string` | Submission status — `draft` or `submitted`. |
| `driver` | `?array` | Associated driver — `{id, name?}`. |
| `vehicle` | `?array` | Associated vehicle — `{id, name?}`. |
| `fields` | `?array` | Submitted fields. Each entry is `{name?, value?}`. |
| `createdAtTime` | `?string` | Creation timestamp (RFC 3339). |
| `updatedAtTime` | `?string` | Last update timestamp (RFC 3339). |
| `submittedAtTime` | `?string` | Submission timestamp (RFC 3339). |

The `FormTemplate` entity (`Samsara\Data\Form\FormTemplate`) exposes:

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Template ID. |
| `name` | `?string` | Template name. |
| `description` | `?string` | Template description. |
| `revision` | `?int` | Template revision number. |
| `fieldDefinitions` | `?array` | Field definitions. Each entry is `{name?, type?}`. |
| `createdAtTime` | `?string` | Creation timestamp (RFC 3339). |
| `updatedAtTime` | `?string` | Last update timestamp (RFC 3339). |

## Related Resources

- [Drivers](drivers.md) — driver records referenced from submissions.
- [Vehicles](vehicles.md) — vehicle records referenced from submissions.
- [Camera Media](camera-media.md) — same request-then-poll pattern for video exports.
- [Query Builder](../query-builder.md) — filters and pagination.

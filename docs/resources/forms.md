---
title: Forms
layout: default
parent: Resources
nav_order: 23
description: "Manage form submissions and access form templates"
permalink: /resources/forms
---

# Forms Resource

Manage form submissions and access form templates.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all form submissions
$submissions = Samsara::forms()->submissions()->get();

// Get all form templates
$templates = Samsara::forms()->templates();

// Create a form submission
$submission = Samsara::forms()->createSubmission([
    'formTemplateId' => 'template-123',
    'driverId' => 'driver-456',
    'fields' => [
        ['id' => 'field-1', 'value' => 'Response text'],
    ],
]);

// Update a form submission
$submission = Samsara::forms()->updateSubmission('submission-id', [
    'fields' => [
        ['id' => 'field-1', 'value' => 'Updated response'],
    ],
]);
```

## Form Submissions Stream

```php
use Carbon\Carbon;

// Get form submissions stream
$submissions = Samsara::forms()
    ->submissionsStream()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();
```

## PDF Exports

```php
// Export form submissions as PDF
$export = Samsara::forms()->exportPdf([
    'formSubmissionIds' => ['submission-1', 'submission-2'],
]);

// Get the export ID
$exportId = $export['id'];

// Retrieve the PDF export
$pdf = Samsara::forms()->getPdfExport($exportId);
```

## Query Builder

```php
// Filter by driver
$submissions = Samsara::forms()
    ->submissions()
    ->whereDriver('driver-123')
    ->get();

// Filter by tag
$submissions = Samsara::forms()
    ->submissions()
    ->whereTag('inspection-forms')
    ->get();
```

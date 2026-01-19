---
title: IFTA
layout: default
parent: Resources
nav_order: 24
description: "Access IFTA reports for tax compliance"
permalink: /resources/ifta
---

# IFTA Resource

Access IFTA (International Fuel Tax Agreement) reports for tax compliance.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get IFTA jurisdiction report
$report = Samsara::ifta()->jurisdictionReport([
    'year' => 2024,
    'quarter' => 1,
]);

// Get IFTA vehicle report
$vehicleReport = Samsara::ifta()->vehicleReport([
    'year' => 2024,
    'quarter' => 1,
]);
```

## CSV Exports

```php
// Request a detail CSV export
$export = Samsara::ifta()->detailCsv([
    'year' => 2024,
    'quarter' => 1,
]);

// Get the export ID
$exportId = $export['id'];

// Retrieve the CSV export
$csv = Samsara::ifta()->getDetailCsv($exportId);
```

## Report Parameters

### Jurisdiction Report

| Parameter | Type | Description |
|-----------|------|-------------|
| `year` | int | Report year |
| `quarter` | int | Report quarter (1-4) |
| `tagIds` | array | Filter by tag IDs |

### Vehicle Report

| Parameter | Type | Description |
|-----------|------|-------------|
| `year` | int | Report year |
| `quarter` | int | Report quarter (1-4) |
| `tagIds` | array | Filter by tag IDs |
| `vehicleIds` | array | Filter by vehicle IDs |

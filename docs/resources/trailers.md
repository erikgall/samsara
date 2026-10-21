---
title: Trailers
nav_order: 3
description: Manage trailers in your Samsara fleet.
permalink: /resources/trailers
---

# Trailers

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [External IDs](#external-ids)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

A trailer is a non-motorized asset tracked by a Samsara trailer gateway. Trailers carry their own tags, external ids, and license plate, and they appear on driver assignments and route stops independently of the towing vehicle. Use this resource to read and manage the trailer roster — telematics for the trailer gateway lives under [Vehicle Locations](vehicle-locations.html) and the dedicated trailer-stats endpoints.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$trailers = Samsara::trailers()->query()->get();

$trailer = Samsara::trailers()->find('trailer-id');
```

## Creating Records

```php
$trailer = Samsara::trailers()->create([
    'name' => 'Trailer T-500',
    'trailerSerialNumber' => 'SN12345678',
]);
```

## Updating Records

```php
$trailer = Samsara::trailers()->update('trailer-id', [
    'name' => 'Trailer T-500 (refurbished)',
]);
```

## Deleting Records

```php
Samsara::trailers()->delete('trailer-id');
```

## Filtering

See [Query Builder](../query-builder.html) for the full filter list. The Trailers resource supports tag, parent-tag, and limit filters out of the box.

```php
$trailers = Samsara::trailers()
    ->query()
    ->whereTag('tag-id')
    ->get();

$trailers = Samsara::trailers()
    ->query()
    ->whereParentTag('parent-tag-id')
    ->get();

$trailers = Samsara::trailers()
    ->query()
    ->limit(10)
    ->get();
```

## External IDs

```php
$trailer = Samsara::trailers()->findByExternalId('asset_id', 'TRL-12345');
```

## Helper Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `getDisplayName()` | `string` | The trailer's `name`, or `"Unknown"` when null. |
| `getExternalId(string $key)` | `?string` | Lookup an external id by namespace key. |
| `getTagIds()` | `array<int, string>` | Flat list of tag ids associated with the trailer. |

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Trailer id. |
| `name` | `?string` | Trailer name. |
| `trailerSerialNumber` | `?string` | The trailer serial number. |
| `licensePlate` | `?string` | License plate. |
| `notes` | `?string` | Free-form notes. |
| `enabledForMobile` | `?bool` | Whether the trailer appears in the driver app. |
| `installedGateway` | `?array{serial?: string, model?: string}` | Installed trailer gateway. |
| `externalIds` | `?array<string, string>` | External id mappings keyed by namespace. |
| `attributes` | `?array` | Trailer attributes returned by item endpoints. |
| `tags` | `?array` | Associated tags (each item exposes `id`, optional `name`, optional `parentTagId`). |

## Related Resources

- [Drivers](drivers.html) — drivers who tow trailers.
- [Routes](routes.html) — routes that reference trailers as stops.
- [Assignments](assignments.html) — trailer assignments to drivers and vehicles.

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

A trailer is a non-motorized asset tracked by a Samsara trailer gateway. Trailers carry their own tags, external ids, and license plate, and they appear on driver assignments and route stops independently of the towing vehicle. Use this resource to read and manage the trailer roster — telematics for the trailer gateway lives under [Vehicle Locations](vehicle-locations.md) and the dedicated trailer-stats endpoints.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$trailers = Samsara::trailers()->all();

$trailer = Samsara::trailers()->find('trailer-id');
```

## Creating Records

```php
$trailer = Samsara::trailers()->create([
    'name' => 'Trailer T-500',
    'assetSerialNumber' => 'SN12345678',
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

See [Query Builder](../query-builder.md) for the full filter list. The Trailers resource supports tag, parent-tag, and limit filters out of the box.

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
| `assetSerialNumber` | `?string` | The trailer asset serial number. |
| `licensePlate` | `?string` | License plate. |
| `notes` | `?string` | Free-form notes. |
| `enabledForMobile` | `?bool` | Whether the trailer appears in the driver app. |
| `externalIds` | `?array<string, string>` | External id mappings keyed by namespace. |
| `tags` | `?array` | Associated tags (each item exposes `id`, optional `name`, optional `parentTagId`). |

## Related Resources

- [Drivers](drivers.md) — drivers who tow trailers.
- [Routes](routes.md) — routes that reference trailers as stops.
- [Assignments](assignments.md) — trailer assignments to drivers and vehicles.

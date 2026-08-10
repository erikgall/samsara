---
title: Equipment
nav_order: 4
description: Read and track non-vehicle equipment in your Samsara fleet.
permalink: /resources/equipment
---

# Equipment

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
  - [By External ID](#by-external-id)
- [Equipment Locations](#equipment-locations)
- [Equipment Stats](#equipment-stats)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Equipment covers the non-vehicle, non-trailer assets in your fleet — generators, light towers, attachments, and similar units that carry a Samsara gateway. Reach for this resource when you need a typed, equipment-specific entity (rather than the generic [Assets](assets.html) view) and when you want the per-equipment locations and stats sub-builders.

The resource owns generic `all` and `find`. Generic `create`, `update`, and `delete` are capability-gated and fail locally. A beta equipment update endpoint exists at `/beta/fleet/equipment/{id}` but is not implemented by this resource.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$equipment = Samsara::equipment()->all();

$item = Samsara::equipment()->find('equipment-id');
```

## Creating Records

`create()` is unsupported and throws `UnsupportedOperationException` before an HTTP request.

## Updating Records

`update()` is unsupported and throws `UnsupportedOperationException` before an HTTP request.

## Deleting Records

`delete()` is unsupported and throws `UnsupportedOperationException` before an HTTP request.

## Filtering

Equipment accepts the standard query builder. See [the query builder reference](../query-builder.html) for the full method list.

```php
$equipment = Samsara::equipment()
    ->query()
    ->whereTag('tag-id')
    ->whereParentTag('parent-tag-id')
    ->get();
```

### By External ID

Equipment external-ID lookup is not part of the current operation contract. `findByExternalId()` throws `UnsupportedOperationException`; use `find()` with a Samsara equipment ID.

```php
$item = Samsara::equipment()->find('equipment-id');
```

## Equipment Locations

Three sub-builders expose location data: a current snapshot, a polling feed, and a historical query.

```php
$locations = Samsara::equipment()->locations()->get();

$feed = Samsara::equipment()->locationsFeed()->get();

$history = Samsara::equipment()
    ->locationsHistory()
    ->between(now()->subDays(7), now())
    ->get();
```

`locations()` returns `EquipmentLocationResult` snapshot objects; call `getLocation()` for an `EquipmentLocation`. `locationsFeed()` and `locationsHistory()` return `EquipmentLocationSeries`; call `getLocations()` for typed readings. All three omit the implicit configured page size because the official cursor operations have no `limit` parameter.

## Equipment Stats

Stats follow the same three-builder shape as locations.

```php
$stats = Samsara::equipment()->stats()->get();

$feed = Samsara::equipment()->statsFeed()->get();

$history = Samsara::equipment()
    ->statsHistory()
    ->between(now()->subDays(7), now())
    ->get();
```

`stats()` returns `EquipmentStats` snapshots. `statsFeed()` and `statsHistory()` return `EquipmentStatsSeries`, whose helpers expose collections of typed readings. These cursor builders also suppress only the implicit configured page size.

## Helper Methods

The `Equipment` entity exposes a small set of helpers:

```php
$item = Samsara::equipment()->find('equipment-id');

$item->getDisplayName();              // string — falls back to "Unknown"
$item->getExternalId('asset_id');     // ?string
$item->getTagIds();                   // array<int, string>
```

## Properties

The `Equipment` entity (`Samsara\Data\Equipment\Equipment`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Equipment ID. |
| `name` | `?string` | Equipment name. |
| `assetSerial` | `?string` | Equipment identification number. |
| `notes` | `?string` | Notes about the equipment. |
| `externalIds` | `?array<string, string>` | External ID mappings. |
| `tags` | `?array` | Associated tags. Each entry is `{id, name?, parentTagId?}`. |
| `installedGateway` | `?array` | Installed gateway info — `{serial?, model?}`. |

## Related Resources

- [Vehicles](vehicles.html) — vehicle equivalents with their own locations and stats sub-builders.
- [Trailers](trailers.html) — trailer equivalents.
- [Assets](assets.html) — combined view across all asset classes.
- [Gateways](gateways.html) — typed reference to the installed gateway.
- [Query Builder](../query-builder.html) — for filtering, pagination, and lazy iteration.

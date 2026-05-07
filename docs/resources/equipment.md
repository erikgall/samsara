---
title: Equipment
nav_order: 4
description: Manage and track non-vehicle equipment in your Samsara fleet.
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

Equipment covers the non-vehicle, non-trailer assets in your fleet — generators, light towers, attachments, and similar units that carry a Samsara gateway. Reach for this resource when you need a typed, equipment-specific entity (rather than the generic [Assets](assets.md) view) and when you want the per-equipment locations and stats sub-builders.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$equipment = Samsara::equipment()->all();

$item = Samsara::equipment()->find('equipment-id');
```

## Creating Records

```php
$item = Samsara::equipment()->create([
    'name'        => 'Generator G-100',
    'assetSerial' => 'GEN12345',
]);
```

## Updating Records

```php
$item = Samsara::equipment()->update('equipment-id', [
    'name' => 'Generator G-100 Updated',
]);
```

## Deleting Records

```php
Samsara::equipment()->delete('equipment-id');
```

## Filtering

Equipment accepts the standard query builder. See [the query builder reference](../query-builder.md) for the full method list.

```php
$equipment = Samsara::equipment()
    ->query()
    ->whereTag('tag-id')
    ->whereParentTag('parent-tag-id')
    ->get();
```

### By External ID

`findByExternalId()` looks up a single record by an `externalIds[key]` mapping you control.

```php
$item = Samsara::equipment()->findByExternalId('asset_id', 'EQ-12345');
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

- [Vehicles](vehicles.md) — vehicle equivalents with their own locations and stats sub-builders.
- [Trailers](trailers.md) — trailer equivalents.
- [Assets](assets.md) — combined view across all asset classes.
- [Gateways](gateways.md) — typed reference to the installed gateway.
- [Query Builder](../query-builder.md) — for filtering, pagination, and lazy iteration.

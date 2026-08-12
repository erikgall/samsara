---
title: Tags
nav_order: 14
description: Manage tags for organizing and grouping fleet resources.
permalink: /resources/tags
---

# Tags

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Hierarchical Tags](#hierarchical-tags)
- [Using Tags For Filtering](#using-tags-for-filtering)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Tags are labels that group every other resource in Samsara — vehicles, drivers, equipment, addresses, alerts. You create a tag once and reference it from anywhere a `whereTag()` filter is supported. Tags also nest: a parent tag (`Regions`) can hold child tags (`West`, `East`), which lets you filter at any level of the hierarchy.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$tags = Samsara::tags()->all();

$tag = Samsara::tags()->find('tag-id');
```

## Creating Records

```php
$tag = Samsara::tags()->create([
    'name' => 'West Coast Fleet',
]);

// Create a child of an existing tag
$child = Samsara::tags()->create([
    'name' => 'California',
    'parentTagId' => 'parent-tag-id',
]);
```

## Updating Records

```php
$tag = Samsara::tags()->update('tag-id', [
    'name' => 'West Coast Operations',
]);
```

## Deleting Records

```php
Samsara::tags()->delete('tag-id');
```

## Filtering

The current list-tags operation declares no query filters or pagination parameters. Use `all()` or an unmodified `query()->get()`, then filter the returned collection in memory when needed. Fluent builder methods do not make unsupported parameters valid for this endpoint.

```php
$tags = Samsara::tags()
    ->query()
    ->get();
```

## Hierarchical Tags

Tags model a parent/child tree. The pattern is to create the parent first, then pass its `id` as `parentTagId` on the child.

```php
$parent = Samsara::tags()->create([
    'name' => 'Regions',
]);

$west = Samsara::tags()->create([
    'name' => 'West',
    'parentTagId' => $parent->id,
]);

$east = Samsara::tags()->create([
    'name' => 'East',
    'parentTagId' => $parent->id,
]);

if ($west->hasParent()) {
    echo "Parent: ".($west->getParent()?->name ?? $west->parentTagId);
}
```

## Using Tags For Filtering

Most other resources accept a `whereTag()` filter that takes a tag id (or array of ids). Use this whenever you want to scope a query to a slice of the fleet without listing each vehicle or driver explicitly.

```php
$vehicles = Samsara::vehicles()
    ->query()
    ->whereTag('west-coast-fleet')
    ->get();

$drivers = Samsara::drivers()
    ->query()
    ->whereTag('delivery-drivers')
    ->get();

$events = Samsara::safetyEvents()
    ->query()
    ->whereTag('monitored-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```

## Helper Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `hasParent()` | `bool` | True when either `parentTagId` or a nested `parentTag` is present. |
| `getParent()` | `?Tag` | Wraps a non-empty nested `parentTag` payload as a typed `Tag`. |
| `getExternalId(string $key)` | `?string` | Returns the exact mapped string, including an empty string, or `null`. |

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `string` | Tag id. |
| `name` | `string` | Tag name. |
| `parentTagId` | `?string` | Id of the parent tag, when the tag is a child. |
| `parentTag` | `?array{id?: string, name?: string}` | Expanded parent tag when included. |
| `externalIds` | `?array<string, string>` | External system ID mappings keyed by namespace. |

## Related Resources

- [Drivers](drivers.html) — filter drivers by tag.
- [Vehicles](vehicles.html) — filter vehicles by tag.
- [Addresses](addresses.html) — filter geofences by tag.

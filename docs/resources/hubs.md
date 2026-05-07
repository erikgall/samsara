---
title: Hubs
nav_order: 32
description: Manage hubs that group your fleet by operating location.
permalink: /resources/hubs
---

# Hubs

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

A hub is a logical grouping that ties drivers, vehicles, and routes to a specific operating location — a yard, terminal, or distribution center. Use this resource when you need to enumerate hubs, create a new one for a region, or attach assets to one. Hubs are not strongly typed: this resource returns generic `Entity` instances (a Laravel `Fluent` wrapper) so you read fields with `$hub->name`, `$hub->id`, and so on.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$hubs = Samsara::hubs()->all();

foreach ($hubs as $hub) {
    echo "{$hub->id}: {$hub->name}\n";
}
```

Look up a single hub by ID:

```php
$hub = Samsara::hubs()->find('hub-id');
```

## Creating Records

```php
$hub = Samsara::hubs()->create([
    'name' => 'West Coast Hub',
]);
```

## Updating Records

```php
$hub = Samsara::hubs()->update('hub-id', [
    'name' => 'West Coast Operations Hub',
]);
```

## Deleting Records

```php
Samsara::hubs()->delete('hub-id');
```

## Filtering

Hubs accept the standard query builder. See [the query builder reference](../query-builder.md) for the full method list.

```php
$hubs = Samsara::hubs()
    ->query()
    ->limit(25)
    ->get();
```

## Properties

This resource does not declare a typed entity, so responses come back as a generic `Samsara\Data\Entity` (a `Fluent` instance). The Samsara API returns these keys for each hub:

| Key | Type | Description |
|----------|------|-------------|
| `id` | `string` | Hub ID. |
| `name` | `string` | Hub name. |

Read each key directly from the entity (`$hub->id`, `$hub->name`) or via `$hub->get('key')`.

## Related Resources

- [Routes](routes.md) — routes that originate or terminate at a hub.
- [Vehicles](vehicles.md) — vehicles assigned to a hub.
- [Query Builder](../query-builder.md) — for filtering, pagination, and lazy iteration.
- [Error Handling](../error-handling.md) — exceptions raised by HTTP failures.

---
title: Hubs
nav_order: 32
description: Read hubs that group your fleet by operating location.
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

A hub is a logical grouping that ties drivers, vehicles, and routes to an operating location. Hubs are not strongly typed: this resource returns generic `Entity` instances.

`HubsResource` exposes the `query()` builder. Generic `all`, `find`, `create`, `update`, and `delete` methods are removed.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$hubs = Samsara::hubs()->query()->get();

foreach ($hubs as $hub) {
    echo "{$hub->id}: {$hub->name}\n";
}
```

## Creating Records

`create()` is removed from this resource.

## Updating Records

`update()` is removed from this resource.

## Deleting Records

`delete()` is removed from this resource.

## Filtering

The list operation accepts `hubIds`, `startTime`, `endTime`, `after`, and `limit`. See [the query builder reference](../query-builder.html) for the fluent method mapping.

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
| `timeZone` | `string` | Hub time zone. |
| `createdAt` | `string` | RFC 3339 creation time. |
| `updatedAt` | `string` | RFC 3339 last-update time. |

Read each key directly from the entity (`$hub->id`, `$hub->name`) or via `$hub->get('key')`.

## Related Resources

- [Routes](routes.html) — routes that originate or terminate at a hub.
- [Vehicles](vehicles.html) — vehicles assigned to a hub.
- [Query Builder](../query-builder.html) — for filtering, pagination, and lazy iteration.
- [Error Handling](../error-handling.html) — exceptions raised by HTTP failures.

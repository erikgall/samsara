---
title: Gateways
nav_order: 17
description: Read Samsara gateway devices installed in vehicles and equipment.
permalink: /resources/gateways
---

# Gateways

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Gateways are the physical Samsara devices installed in vehicles, trailers, and equipment that report telemetry back to the API. Reach for this resource when you need to enumerate the gateways your fleet owns or look one up by ID. Gateways are typically associated with another asset — when you load a `Vehicle` or `Equipment` entity, the gateway data is already exposed there.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$gateways = Samsara::gateways()->all();

foreach ($gateways as $gateway) {
    echo "{$gateway->serial} ({$gateway->model})\n";
}
```

Look up a single gateway by ID:

```php
$gateway = Samsara::gateways()->find('gateway-id');
```

## Creating Records

> **Note:** Gateway provisioning happens through Samsara's hardware fulfillment process, not the API. This resource does not support `create()`.

## Updating Records

> **Note:** This resource does not support `update()`.

## Deleting Records

> **Note:** This resource does not support `delete()`.

## Properties

The `Gateway` entity (`Samsara\Data\Vehicle\Gateway`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `serial` | `?string` | Gateway serial number. |
| `model` | `?string` | Gateway model identifier. |

> **Note:** The Samsara API may return additional fields on the response payload. Access them through the underlying `Fluent` accessors (`$gateway->get('field')`) until the entity is expanded.

## Related Resources

- [Vehicles](vehicles.md) — vehicles that carry an installed gateway.
- [Equipment](equipment.md) — equipment with an `installedGateway` property.
- [Query Builder](../query-builder.md) — for filtering, pagination, and lazy iteration.
- [Error Handling](../error-handling.md) — exceptions raised by HTTP failures.

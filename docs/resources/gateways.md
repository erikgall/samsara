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

Gateways are the physical Samsara devices installed in vehicles, trailers, and equipment that report telemetry back to the API. Reach for this resource to enumerate, create, or delete current gateway records. Gateway data is also exposed on `Vehicle` and `Equipment` entities.

> **Package/upstream boundary:** Current upstream supports list and create at `/gateways`, plus delete by ID. It does not define find-by-ID or update-by-ID. The resource capability gate supports only `all()`, `create()`, and `delete()`; unsupported generic calls fail locally before HTTP.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$gateways = Samsara::gateways()->all();

foreach ($gateways as $gateway) {
    echo "{$gateway->serial} ({$gateway->model})\n";
}
```

## Creating Records

```php
$gateway = Samsara::gateways()->create([
    'serial' => 'GFRV-43N-VGX',
]);
```

## Updating Records

`update()` and `find()` are unsupported and fail locally. The supported generic operations are `all`, `create`, and `delete`.

## Deleting Records

```php
Samsara::gateways()->delete('gateway-id');
```

## Properties

The `Gateway` entity (`Samsara\Data\Vehicle\Gateway`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `serial` | `?string` | Gateway serial number. |
| `model` | `?string` | Gateway model identifier. |
| `accessoryDevices` | `?array` | Accessory devices connected to the gateway. |
| `asset` | `?array<string, mixed>` | Attached asset. |
| `connectionStatus` | `?array<string, mixed>` | Connection-status details. |
| `dataUsageLast30Days` | `?array<string, mixed>` | Recent data-usage details. |

## Related Resources

- [Vehicles](vehicles.html) — vehicles that carry an installed gateway.
- [Equipment](equipment.html) — equipment with an `installedGateway` property.
- [Query Builder](../query-builder.html) — for filtering, pagination, and lazy iteration.
- [Error Handling](../error-handling.html) — exceptions raised by HTTP failures.

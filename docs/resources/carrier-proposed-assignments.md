---
title: Carrier Proposed Assignments
nav_order: 36
description: Carrier-proposed driver, vehicle, and trailer assignments.
permalink: /resources/carrier-proposed-assignments
---

# Carrier Proposed Assignments

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Carrier-proposed assignments represent assignment suggestions a carrier surfaces to drivers. The resource supports generic `all`, `create`, and `delete`, plus `query()` for cursor-aware collection access. `find()` and `update()` are rejected locally. Reach for [Assignments](assignments.html) when you need confirmed assignments.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$proposals = Samsara::carrierProposedAssignments()->all();
```

## Creating Records

```php
$proposal = Samsara::carrierProposedAssignments()->create([
    'driverId' => 'driver-id',
    'vehicleId' => 'vehicle-id',
]);
```

The accepted payload shape is dictated by the Samsara API. Pass the same keys you would use against the REST endpoint directly.

## Updating Records

`update()` is unsupported and throws `UnsupportedOperationException` before HTTP.

## Deleting Records

```php
Samsara::carrierProposedAssignments()->delete('proposal-id');
```

## Properties

The resource returns a generic `Entity` (a `Fluent` instance) keyed by the fields the Samsara API includes in the response. Access values with array syntax or `Fluent` accessors:

```php
$proposal = $proposals->first();

$proposal['id'];
$proposal['driverId'];
$proposal['vehicleId'];
```

## Related Resources

- [Assignments](assignments.html) — driver, vehicle, and trailer assignments after they are confirmed.
- [Drivers](drivers.html) — the drivers a proposal targets.
- [Vehicles](vehicles.html) — the vehicles a proposal references.
- [Trailers](trailers.html) — the trailers a proposal references.

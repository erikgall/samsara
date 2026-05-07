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
- [Filtering](#filtering)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Carrier-proposed assignments represent assignment suggestions a carrier surfaces to drivers — pairings of a driver with a vehicle or trailer that the driver may accept on the Samsara Driver app. The resource exposes the standard CRUD methods inherited from the base `Resource` class against the `/fleet/carrier-proposed-assignments` endpoint. Reach for the regular [Assignments](assignments.md) resource when you need to read or manage driver, vehicle, and trailer assignments that have already been confirmed.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$proposals = Samsara::carrierProposedAssignments()->all();

$proposal = Samsara::carrierProposedAssignments()->find('proposal-id');
```

You may also build a query when you need cursor pagination or query parameters supported by the endpoint:

```php
$proposals = Samsara::carrierProposedAssignments()
    ->query()
    ->limit(50)
    ->paginate();
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

```php
$proposal = Samsara::carrierProposedAssignments()->update('proposal-id', [
    'vehicleId' => 'replacement-vehicle-id',
]);
```

## Deleting Records

```php
Samsara::carrierProposedAssignments()->delete('proposal-id');
```

## Filtering

The resource inherits the generic [query builder](../query-builder.md). Endpoint-specific filters depend on the Samsara API; consult the [Samsara developer reference](https://developers.samsara.com/) for the supported parameters.

## Properties

The resource returns a generic `Entity` (a `Fluent` instance) keyed by the fields the Samsara API includes in the response. Access values with array syntax or `Fluent` accessors:

```php
$proposal['id'];
$proposal['driverId'];
$proposal['vehicleId'];
```

## Related Resources

- [Assignments](assignments.md) — driver, vehicle, and trailer assignments after they are confirmed.
- [Drivers](drivers.md) — the drivers a proposal targets.
- [Vehicles](vehicles.md) — the vehicles a proposal references.
- [Trailers](trailers.md) — the trailers a proposal references.

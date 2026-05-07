---
title: Tachograph
nav_order: 29
description: Access tachograph data for EU compliance reporting.
permalink: /resources/tachograph
---

# Tachograph

- [Introduction](#introduction)
- [Driver Activity History](#driver-activity-history)
- [Driver Files History](#driver-files-history)
- [Vehicle Files History](#vehicle-files-history)
- [Filtering](#filtering)
- [Related Resources](#related-resources)

## Introduction

Tachograph endpoints expose the EU regulatory data captured by tachograph-equipped vehicles — driver activity (driving, rest, work, available), and the periodic driver- and vehicle-card files used to satisfy member-state audits. The resource is read-only and **only available for EU organizations**. North American fleets see empty responses.

> **Note:** This resource does not expose `find()`, `all()`, or any of the create/update/delete verbs. Each method below returns a query builder you must drive with `between()` and the standard filters.

## Driver Activity History

Driver activity returns the duty-status timeline (driving, work, rest, available) for one or more drivers across a time window.

```php
use Samsara\Facades\Samsara;

$activities = Samsara::tachograph()
    ->driverActivityHistory()
    ->between(now()->subDays(7), now())
    ->get();
```

## Driver Files History

Driver files history returns the periodic driver-card downloads — the digital files the driver's card emits on a regulated cadence.

```php
$driverFiles = Samsara::tachograph()
    ->driverFilesHistory()
    ->between(now()->subDays(30), now())
    ->get();
```

## Vehicle Files History

Vehicle files history returns the corresponding vehicle-unit downloads.

```php
$vehicleFiles = Samsara::tachograph()
    ->vehicleFilesHistory()
    ->between(now()->subDays(30), now())
    ->get();
```

## Filtering

Each builder accepts the filters described in [Query Builder](../query-builder.md). The most common combinations are by driver, vehicle, and tag.

```php
// Filter activity by driver
$activities = Samsara::tachograph()
    ->driverActivityHistory()
    ->whereDriver('driver-id')
    ->between(now()->subDays(7), now())
    ->get();

// Filter vehicle files by vehicle
$vehicleFiles = Samsara::tachograph()
    ->vehicleFilesHistory()
    ->whereVehicle('vehicle-id')
    ->between(now()->subDays(30), now())
    ->get();

// Filter activity by tag
$activities = Samsara::tachograph()
    ->driverActivityHistory()
    ->whereTag('eu-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```

## Related Resources

- [Drivers](drivers.md) — driver records referenced by tachograph activity.
- [Hours of Service](hours-of-service.md) — North American HOS equivalent.
- [Vehicles](vehicles.md) — vehicles whose tachograph units produce the files.

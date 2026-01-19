---
title: Tachograph
layout: default
parent: Resources
nav_order: 29
description: "Access tachograph data for EU compliance"
permalink: /resources/tachograph
---

# Tachograph Resource (EU Only)

Access tachograph data for EU compliance requirements.

> **Note:** This resource is only available for EU organizations using tachograph-equipped vehicles.

## Driver Activity History

```php
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get driver activity history
$activities = Samsara::tachograph()
    ->driverActivityHistory()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();
```

## Driver Files History

```php
// Get driver files history
$driverFiles = Samsara::tachograph()
    ->driverFilesHistory()
    ->between(now()->subDays(30), now())
    ->get();
```

## Vehicle Files History

```php
// Get vehicle files history
$vehicleFiles = Samsara::tachograph()
    ->vehicleFilesHistory()
    ->between(now()->subDays(30), now())
    ->get();
```

## Query Builder

```php
// Filter by driver
$activities = Samsara::tachograph()
    ->driverActivityHistory()
    ->whereDriver('driver-123')
    ->between(now()->subDays(7), now())
    ->get();

// Filter by vehicle
$vehicleFiles = Samsara::tachograph()
    ->vehicleFilesHistory()
    ->whereVehicle('vehicle-456')
    ->between(now()->subDays(30), now())
    ->get();

// Filter by tag
$activities = Samsara::tachograph()
    ->driverActivityHistory()
    ->whereTag('eu-fleet')
    ->between(now()->subDays(7), now())
    ->get();
```

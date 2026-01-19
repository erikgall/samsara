---
title: Idling
layout: default
parent: Resources
nav_order: 25
description: "Access vehicle idling events"
permalink: /resources/idling
---

# Idling Resource

Access vehicle idling events for monitoring fuel efficiency and compliance.

## Basic Usage

```php
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get idling events
$events = Samsara::idling()
    ->events()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();
```

## Query Builder

```php
// Filter by vehicle
$events = Samsara::idling()
    ->events()
    ->whereVehicle('vehicle-123')
    ->between(now()->subDays(7), now())
    ->get();

// Filter by tag
$events = Samsara::idling()
    ->events()
    ->whereTag('delivery-fleet')
    ->between(now()->subDays(7), now())
    ->get();

// Limit results
$events = Samsara::idling()
    ->events()
    ->limit(100)
    ->get();
```

## Lazy Loading

```php
// Stream through large datasets
Samsara::idling()
    ->events()
    ->between(now()->subMonth(), now())
    ->lazy(500)
    ->each(function ($event) {
        // Process each idling event
    });
```

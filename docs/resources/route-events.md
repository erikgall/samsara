---
title: Route Events
layout: default
parent: Resources
nav_order: 35
description: "Access route events for tracking dispatch operations"
permalink: /resources/route-events
---

# Route Events Resource

Access route events for tracking dispatch and delivery operations.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all route events
$events = Samsara::routeEvents()->all();

// Find a route event
$event = Samsara::routeEvents()->find('event-id');
```

## Query Builder

```php
use Carbon\Carbon;

// Get route events with query builder
$events = Samsara::routeEvents()
    ->query()
    ->get();

// Filter by time range
$events = Samsara::routeEvents()
    ->query()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();

// Filter by tag
$events = Samsara::routeEvents()
    ->query()
    ->whereTag('delivery-routes')
    ->get();

// Limit results
$events = Samsara::routeEvents()
    ->query()
    ->limit(100)
    ->get();
```

## Lazy Loading

```php
// Stream through large datasets
Samsara::routeEvents()
    ->query()
    ->between(now()->subMonth(), now())
    ->lazy(500)
    ->each(function ($event) {
        // Process each route event
    });
```

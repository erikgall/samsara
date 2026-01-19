---
title: Query Builder
layout: default
nav_order: 4
description: "Fluent query builder for filtering, pagination, and data retrieval"
permalink: /query-builder
---

# Query Builder

The Samsara SDK provides a fluent query builder for filtering, pagination, and data retrieval.

## Basic Usage

Access the query builder via the `query()` method on any resource:

```php
use Samsara\Facades\Samsara;

$drivers = Samsara::drivers()
    ->query()
    ->whereTag('tag-123')
    ->get();
```

Some resources return a query builder directly:

```php
$stats = Samsara::vehicleStats()
    ->types(['gps', 'engineStates'])
    ->get();
```

## Filtering

### By Tag

Filter by tag IDs:

```php
// Single tag
->whereTag('tag-123')

// Multiple tags
->whereTag(['tag-123', 'tag-456'])
```

### By Parent Tag

Filter by parent tag IDs:

```php
->whereParentTag('parent-tag-id')
```

### By Vehicle

Filter by vehicle IDs:

```php
->whereVehicle('vehicle-123')
->whereVehicle(['vehicle-123', 'vehicle-456'])
```

### By Driver

Filter by driver IDs:

```php
->whereDriver('driver-123')
->whereDriver(['driver-123', 'driver-456'])
```

### By Attribute

Filter by attribute value IDs:

```php
->whereAttribute('attribute-value-id')
```

### Custom Filters

Add arbitrary query parameters:

```php
->where('customField', 'value')
```

## Time Ranges

### Between Two Dates

```php
use Carbon\Carbon;

// Using strings
->between('2024-01-01T00:00:00Z', '2024-01-31T23:59:59Z')

// Using Carbon instances
->between(Carbon::now()->subDays(7), Carbon::now())
```

### Start and End Time

```php
->startTime('2024-01-01T00:00:00Z')
->endTime('2024-01-31T23:59:59Z')
```

### Updated After

Filter records updated after a timestamp:

```php
->updatedAfter('2024-01-01T00:00:00Z')
```

### Created After

Filter records created after a timestamp:

```php
->createdAfter('2024-01-01T00:00:00Z')
```

## Stat Types

For telemetry resources, specify which stat types to retrieve:

```php
use Samsara\Enums\VehicleStatType;

// Using strings
->types(['gps', 'engineStates', 'fuelPercents'])

// Using enum
->types([
    VehicleStatType::GPS,
    VehicleStatType::ENGINE_STATES,
    VehicleStatType::FUEL_PERCENTS,
])
```

## Decorations

Include decorated values in the response:

```php
->withDecorations(['address', 'reverseGeo'])
```

## Expanding Nested Data

Include related data in the response:

```php
->expand(['driver', 'vehicle'])
```

## Pagination

### Limit Results

```php
// Limit to 50 results
->limit(50)

// Alias for limit
->take(50)
```

### Cursor-Based Pagination

```php
// Get first page
$paginator = Samsara::vehicles()
    ->query()
    ->limit(50)
    ->paginate();

// Iterate through items
foreach ($paginator as $vehicle) {
    echo $vehicle->name;
}

// Get next page
if ($paginator->hasMorePages()) {
    $nextPage = $paginator->nextPage();
}
```

### Manual Cursor Navigation

```php
// Start after a specific cursor
->after('cursor-token-from-previous-response')
```

## Execution Methods

### Get All Results

Returns an `EntityCollection`:

```php
$drivers = Samsara::drivers()->query()->get();
```

### Get First Result

Returns a single entity or `null`:

```php
$driver = Samsara::drivers()
    ->query()
    ->whereTag('tag-123')
    ->first();
```

### Paginate

Returns a `CursorPaginator`:

```php
$paginator = Samsara::vehicles()
    ->query()
    ->paginate(50);

foreach ($paginator as $vehicle) {
    // Process vehicle
}
```

### Lazy Loading

Returns a `LazyCollection` for memory-efficient processing:

```php
Samsara::vehicleStats()
    ->types(['gps'])
    ->between($start, $end)
    ->lazy()
    ->each(function ($stat) {
        // Process each stat without loading all into memory
    });
```

## Complete Examples

### Fleet Overview

```php
// Get all active drivers with their tags
$drivers = Samsara::drivers()
    ->active()
    ->get();

// Get vehicles by tag
$vehicles = Samsara::vehicles()
    ->query()
    ->whereTag('fleet-a')
    ->get();
```

### Vehicle Telemetry

```php
use Carbon\Carbon;

// Get GPS and fuel data for the last 24 hours
$stats = Samsara::vehicleStats()
    ->types(['gps', 'fuelPercents', 'engineStates'])
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->between(Carbon::now()->subDay(), Carbon::now())
    ->withDecorations(['address'])
    ->get();
```

### Hours of Service

```php
// Get HOS logs for specific drivers
$logs = Samsara::hoursOfService()
    ->logs()
    ->whereDriver(['driver-1', 'driver-2'])
    ->between($startOfWeek, $endOfWeek)
    ->get();

// Get current HOS clocks
$clocks = Samsara::hoursOfService()
    ->clocks()
    ->whereDriver('driver-1')
    ->get();
```

### Processing Large Datasets

```php
// Stream through all vehicle stats
$count = 0;

Samsara::vehicleStats()
    ->types(['gps'])
    ->between($start, $end)
    ->lazy(100) // Chunk size
    ->each(function ($stat) use (&$count) {
        $count++;
        // Process stat
    });

echo "Processed {$count} stats";
```

## Building Queries Programmatically

```php
$query = Samsara::vehicles()->query();

if ($tagId = request('tag_id')) {
    $query->whereTag($tagId);
}

if ($updatedAfter = request('updated_after')) {
    $query->updatedAfter($updatedAfter);
}

$vehicles = $query->limit(100)->get();
```

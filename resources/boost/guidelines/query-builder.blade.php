# Samsara ELD Laravel SDK - Query Builder Guidelines

## Overview

The SDK provides a fluent query builder for filtering, paginating, and streaming API results. Query builders are available on resources that support listing operations.

## Creating a Query Builder

@verbatim
<code-snippet name="Create a query builder instance" lang="php">
use Samsara\Facades\Samsara;

// Start a new query
$query = Samsara::drivers()->query();
$query = Samsara::vehicles()->query();
$query = Samsara::vehicleStats()->query();
</code-snippet>
@endverbatim

## Basic Filtering

### Filter by Tag

@verbatim
<code-snippet name="Filter results by tag IDs" lang="php">
use Samsara\Facades\Samsara;

// Filter by single tag
$drivers = Samsara::drivers()
    ->query()
    ->whereTag('tag-123')
    ->get();

// Filter by multiple tags
$vehicles = Samsara::vehicles()
    ->query()
    ->whereTag(['tag-123', 'tag-456'])
    ->get();
</code-snippet>
@endverbatim

### Filter by Parent Tag

@verbatim
<code-snippet name="Filter results by parent tag IDs" lang="php">
use Samsara\Facades\Samsara;

$vehicles = Samsara::vehicles()
    ->query()
    ->whereParentTag('parent-tag-123')
    ->get();
</code-snippet>
@endverbatim

### Filter by Driver

@verbatim
<code-snippet name="Filter results by driver IDs" lang="php">
use Samsara\Facades\Samsara;

// Filter trips by driver
$trips = Samsara::trips()
    ->query()
    ->whereDriver('driver-123')
    ->get();

// Filter HOS logs by multiple drivers
$logs = Samsara::hoursOfService()
    ->logs()
    ->whereDriver(['driver-123', 'driver-456'])
    ->get();
</code-snippet>
@endverbatim

### Filter by Vehicle

@verbatim
<code-snippet name="Filter results by vehicle IDs" lang="php">
use Samsara\Facades\Samsara;

// Filter vehicle stats by vehicle
$stats = Samsara::vehicleStats()
    ->query()
    ->whereVehicle('vehicle-123')
    ->get();

// Filter trips by multiple vehicles
$trips = Samsara::trips()
    ->query()
    ->whereVehicle(['vehicle-123', 'vehicle-456'])
    ->get();
</code-snippet>
@endverbatim

### Filter by Attribute

@verbatim
<code-snippet name="Filter results by attribute value IDs" lang="php">
use Samsara\Facades\Samsara;

$drivers = Samsara::drivers()
    ->query()
    ->whereAttribute('attribute-value-123')
    ->get();
</code-snippet>
@endverbatim

### Custom Filters

@verbatim
<code-snippet name="Add custom filter parameters" lang="php">
use Samsara\Facades\Samsara;

// Filter drivers by activation status
$activeDrivers = Samsara::drivers()
    ->query()
    ->where('driverActivationStatus', 'active')
    ->get();

// Filter by external ID
$driver = Samsara::drivers()
    ->query()
    ->where('externalIds[mySystem]', 'EXT-12345')
    ->first();
</code-snippet>
@endverbatim

## Time-Based Queries

### Filter by Time Range

@verbatim
<code-snippet name="Filter results within a time range" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Using between() with Carbon instances
$trips = Samsara::trips()
    ->query()
    ->between(
        Carbon::now()->subDays(7),
        Carbon::now()
    )
    ->get();

// Using between() with strings
$logs = Samsara::hoursOfService()
    ->logs()
    ->between('2024-01-01T00:00:00Z', '2024-01-31T23:59:59Z')
    ->get();
</code-snippet>
@endverbatim

### Set Start and End Times Separately

@verbatim
<code-snippet name="Set start and end times independently" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

$stats = Samsara::vehicleStats()
    ->history()
    ->startTime(Carbon::now()->subHours(24))
    ->endTime(Carbon::now())
    ->whereVehicle('vehicle-123')
    ->get();
</code-snippet>
@endverbatim

### Filter by Updated/Created Timestamps

@verbatim
<code-snippet name="Filter by modification timestamps" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get drivers updated in the last hour
$recentlyUpdated = Samsara::drivers()
    ->query()
    ->updatedAfter(Carbon::now()->subHour())
    ->get();

// Get addresses created after a specific date
$newAddresses = Samsara::addresses()
    ->query()
    ->createdAfter('2024-06-01T00:00:00Z')
    ->get();
</code-snippet>
@endverbatim

## Result Limiting

### Limit Results

@verbatim
<code-snippet name="Limit the number of results returned" lang="php">
use Samsara\Facades\Samsara;

// Get first 10 drivers
$drivers = Samsara::drivers()
    ->query()
    ->limit(10)
    ->get();

// take() is an alias for limit()
$vehicles = Samsara::vehicles()
    ->query()
    ->take(25)
    ->get();
</code-snippet>
@endverbatim

### Get First Result

@verbatim
<code-snippet name="Get only the first matching result" lang="php">
use Samsara\Facades\Samsara;

// Returns entity or null
$driver = Samsara::drivers()
    ->query()
    ->where('driverActivationStatus', 'active')
    ->first();
</code-snippet>
@endverbatim

## Executing Queries

### Get All Results

@verbatim
<code-snippet name="Execute query and get all results" lang="php">
use Samsara\Facades\Samsara;

// get() returns an EntityCollection
$drivers = Samsara::drivers()
    ->query()
    ->whereTag('fleet-a')
    ->get();

// Work with results
foreach ($drivers as $driver) {
    echo $driver->name;
}

// Collection methods available
$names = $drivers->pluck('name');
$count = $drivers->count();
</code-snippet>
@endverbatim

## Pagination

The SDK uses cursor-based pagination for efficient traversal of large datasets.

### Basic Pagination

@verbatim
<code-snippet name="Paginate query results" lang="php">
use Samsara\Facades\Samsara;

// Paginate with default page size
$page = Samsara::drivers()->query()->paginate();

// Paginate with custom page size
$page = Samsara::vehicles()
    ->query()
    ->paginate(50);

// Access results
$items = $page->items();

// Check for more pages
if ($page->hasMorePages()) {
    $nextPage = $page->nextPage();
}
</code-snippet>
@endverbatim

### Cursor Navigation

@verbatim
<code-snippet name="Navigate through paginated results" lang="php">
use Samsara\Facades\Samsara;

// Get first page
$page = Samsara::drivers()->query()->paginate(100);

while ($page->hasMorePages()) {
    foreach ($page->items() as $driver) {
        processDriver($driver);
    }

    // Get next page
    $page = $page->nextPage();
}

// Process remaining items on last page
foreach ($page->items() as $driver) {
    processDriver($driver);
}
</code-snippet>
@endverbatim

### Manual Cursor Control

@verbatim
<code-snippet name="Set pagination cursor manually" lang="php">
use Samsara\Facades\Samsara;

// Continue from a saved cursor position
$drivers = Samsara::drivers()
    ->query()
    ->after('cursor-from-previous-request')
    ->limit(100)
    ->get();
</code-snippet>
@endverbatim

## Lazy Loading (Memory-Efficient Streaming)

For large datasets, use lazy loading to stream results without loading everything into memory.

### Basic Lazy Loading

@verbatim
<code-snippet name="Stream results with lazy loading" lang="php">
use Samsara\Facades\Samsara;

// Returns a LazyCollection
$drivers = Samsara::drivers()
    ->query()
    ->lazy();

// Process one at a time - memory efficient
foreach ($drivers as $driver) {
    processDriver($driver);
}
</code-snippet>
@endverbatim

### Lazy Loading with Chunk Size

@verbatim
<code-snippet name="Control lazy loading chunk size" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Fetch 500 records per API call
$logs = Samsara::hoursOfService()
    ->logs()
    ->between(Carbon::now()->subMonth(), Carbon::now())
    ->lazy(500);

foreach ($logs as $log) {
    // Process each log entry
    processLog($log);
}
</code-snippet>
@endverbatim

### Combining with Collection Methods

@verbatim
<code-snippet name="Use collection methods with lazy loading" lang="php">
use Samsara\Facades\Samsara;

// LazyCollection supports most collection methods
$activeDriverNames = Samsara::drivers()
    ->query()
    ->lazy()
    ->filter(fn ($driver) => $driver->status === 'active')
    ->map(fn ($driver) => $driver->name)
    ->take(100)
    ->all();
</code-snippet>
@endverbatim

## Advanced Options

### Specify Stat Types

@verbatim
<code-snippet name="Request specific telemetry types" lang="php">
use Samsara\Facades\Samsara;

// Get specific vehicle stat types
$stats = Samsara::vehicleStats()
    ->query()
    ->types(['gps', 'fuelPercents', 'engineStates'])
    ->whereVehicle('vehicle-123')
    ->get();
</code-snippet>
@endverbatim

### Expand Related Data

@verbatim
<code-snippet name="Include related data in response" lang="php">
use Samsara\Facades\Samsara;

$vehicles = Samsara::vehicles()
    ->query()
    ->expand(['tags', 'attributes'])
    ->get();
</code-snippet>
@endverbatim

### Include Decorations

@verbatim
<code-snippet name="Include additional decorations in response" lang="php">
use Samsara\Facades\Samsara;

$vehicles = Samsara::vehicles()
    ->query()
    ->withDecorations(['externalIds'])
    ->get();
</code-snippet>
@endverbatim

## Complex Query Examples

### Chaining Multiple Filters

@verbatim
<code-snippet name="Build complex queries with multiple filters" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get vehicle stats for specific vehicles and time range
$stats = Samsara::vehicleStats()
    ->history()
    ->whereVehicle(['vehicle-1', 'vehicle-2', 'vehicle-3'])
    ->between(Carbon::now()->subWeek(), Carbon::now())
    ->types(['gps', 'obdOdometerMeters'])
    ->limit(1000)
    ->get();
</code-snippet>
@endverbatim

### Tag-Based Fleet Query

@verbatim
<code-snippet name="Query fleet by organizational tags" lang="php">
use Samsara\Facades\Samsara;

// Get all active drivers in West Coast fleet
$westCoastDrivers = Samsara::drivers()
    ->query()
    ->whereTag('west-coast-fleet')
    ->where('driverActivationStatus', 'active')
    ->get();

// Get their HOS violations
foreach ($westCoastDrivers as $driver) {
    $violations = Samsara::hoursOfService()
        ->violations()
        ->whereDriver($driver->getId())
        ->between(now()->subDays(30), now())
        ->get();
}
</code-snippet>
@endverbatim

### Efficient Large Dataset Processing

@verbatim
<code-snippet name="Process large datasets efficiently" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Stream through all trips from the past year
Samsara::trips()
    ->query()
    ->between(Carbon::now()->subYear(), Carbon::now())
    ->lazy(1000)
    ->each(function ($trip) {
        // Process each trip without memory buildup
        DB::table('trip_archive')->insert([
            'samsara_id' => $trip->getId(),
            'driver_id' => $trip->driverId,
            'start_time' => $trip->startTime,
            'end_time' => $trip->endTime,
            'distance' => $trip->distanceMeters,
        ]);
    });
</code-snippet>
@endverbatim

## Query Builder Method Reference

| Method | Description |
|--------|-------------|
| `where($key, $value)` | Add custom filter |
| `whereTag($ids)` | Filter by tag IDs |
| `whereParentTag($ids)` | Filter by parent tag IDs |
| `whereDriver($ids)` | Filter by driver IDs |
| `whereVehicle($ids)` | Filter by vehicle IDs |
| `whereAttribute($ids)` | Filter by attribute value IDs |
| `between($start, $end)` | Set time range |
| `startTime($time)` | Set start time |
| `endTime($time)` | Set end time |
| `updatedAfter($time)` | Filter by updated timestamp |
| `createdAfter($time)` | Filter by created timestamp |
| `limit($count)` | Limit results |
| `take($count)` | Alias for limit |
| `after($cursor)` | Set pagination cursor |
| `types($types)` | Specify stat types |
| `expand($relations)` | Expand related data |
| `withDecorations($decorations)` | Include decorations |
| `get()` | Execute and return EntityCollection |
| `first()` | Get first result or null |
| `paginate($perPage)` | Execute with pagination |
| `lazy($chunkSize)` | Return LazyCollection for streaming |

# Samsara Query Builder

Fluent query builder for filtering, paginating, and streaming API results.

## Filtering

@verbatim
<code-snippet name="Query builder filters" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

$drivers = Samsara::drivers()
    ->query()
    ->whereTag('fleet-west')           // Filter by tag ID(s)
    ->whereParentTag('parent-123')     // Filter by parent tag
    ->whereDriver('driver-123')        // Filter by driver ID(s)
    ->whereVehicle(['v-1', 'v-2'])     // Filter by vehicle ID(s)
    ->whereAttribute('attr-123')       // Filter by attribute value
    ->where('status', 'active')        // Custom filter
    ->get();
</code-snippet>
@endverbatim

## Time-Based Queries

@verbatim
<code-snippet name="Time range filters" lang="php">
use Carbon\Carbon;

$trips = Samsara::trips()
    ->query()
    ->between(Carbon::now()->subDays(7), Carbon::now())  // Time range
    ->get();

$logs = Samsara::hoursOfService()
    ->logs()
    ->startTime('2024-01-01T00:00:00Z')
    ->endTime('2024-01-31T23:59:59Z')
    ->get();

$drivers = Samsara::drivers()
    ->query()
    ->updatedAfter(Carbon::now()->subHour())  // Modified since
    ->createdAfter('2024-06-01T00:00:00Z')    // Created since
    ->get();
</code-snippet>
@endverbatim

## Execution Methods

@verbatim
<code-snippet name="Query execution" lang="php">
// Get all results as EntityCollection
$drivers = Samsara::drivers()->query()->whereTag('fleet-a')->get();

// Get first result or null
$driver = Samsara::drivers()->query()->where('status', 'active')->first();

// Limit results
$drivers = Samsara::drivers()->query()->limit(10)->get();
</code-snippet>
@endverbatim

## Pagination

@verbatim
<code-snippet name="Cursor-based pagination" lang="php">
$page = Samsara::drivers()->query()->paginate(50);

foreach ($page->items() as $driver) {
    processDriver($driver);
}

while ($page->hasMorePages()) {
    $page = $page->nextPage();
    foreach ($page->items() as $driver) {
        processDriver($driver);
    }
}

// Manual cursor control
$drivers = Samsara::drivers()->query()->after('cursor-token')->limit(100)->get();
</code-snippet>
@endverbatim

## Lazy Loading (Memory-Efficient)

@verbatim
<code-snippet name="Stream large datasets" lang="php">
// Returns LazyCollection - processes one at a time
Samsara::hoursOfService()
    ->logs()
    ->between(Carbon::now()->subMonth(), Carbon::now())
    ->lazy(500)  // Chunk size per API call
    ->each(function ($log) {
        processLog($log);
    });

// Chain with collection methods
$names = Samsara::drivers()
    ->query()
    ->lazy()
    ->filter(fn ($d) => $d->status === 'active')
    ->map(fn ($d) => $d->name)
    ->take(100)
    ->all();
</code-snippet>
@endverbatim

## Vehicle Stats

@verbatim
<code-snippet name="Telemetry data queries" lang="php">
// Current snapshot
$stats = Samsara::vehicleStats()
    ->current()
    ->whereVehicle(['v-1', 'v-2'])
    ->types(['gps', 'fuelPercents', 'engineStates'])
    ->get();

// Historical data
$history = Samsara::vehicleStats()
    ->history()
    ->whereVehicle('v-123')
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->types(['gps', 'obdOdometerMeters'])
    ->get();

// Real-time feed
$feed = Samsara::vehicleStats()->feed()->types(['gps'])->get();
</code-snippet>
@endverbatim

## Query Builder Methods

| Method | Description |
|--------|-------------|
| `whereTag($ids)` | Filter by tag IDs |
| `whereDriver($ids)` | Filter by driver IDs |
| `whereVehicle($ids)` | Filter by vehicle IDs |
| `where($key, $value)` | Custom filter |
| `between($start, $end)` | Time range |
| `updatedAfter($time)` | Modified since |
| `limit($n)` / `take($n)` | Limit results |
| `after($cursor)` | Pagination cursor |
| `types($types)` | Stat types for telemetry |
| `expand($relations)` | Include related data |
| `get()` | Execute → EntityCollection |
| `first()` | Execute → Entity or null |
| `paginate($perPage)` | Execute → CursorPaginator |
| `lazy($chunkSize)` | Execute → LazyCollection |

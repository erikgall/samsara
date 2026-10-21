---
title: Query Builder
layout: default
nav_order: 4
description: "Fluent query builder for filtering, pagination, and data retrieval"
permalink: /query-builder
---

# Query Builder

- [Introduction](#introduction)
- [Building Queries](#building-queries)
- [Filtering](#filtering)
- [Modifiers](#modifiers)
  - [Time ranges](#time-ranges)
  - [Stat types](#stat-types)
  - [Decorations](#decorations)
  - [Expanding nested data](#expanding-nested-data)
- [Pagination](#pagination)
  - [Cursor-Based Pagination](#cursor-based-pagination)
- [Execution Methods](#execution-methods)
- [Working with Results](#working-with-results)
- [Escape Hatch](#escape-hatch)

## Introduction

The query builder is the fluent interface you reach for whenever you need to filter, paginate, or stream records from a Samsara resource. You start a builder by calling `query()` on a resource, or by calling a builder-returning helper such as `current()` or `history()` on resources that expose multiple endpoints. Resources like [Vehicle Stats](resources/vehicle-stats.html) also expose typed shortcuts (`current()`, `history()`, `feed()`, `gps()`, `engineStates()`, etc.) that return a pre-configured builder — prefer those when you know which endpoint or stat type you want.

The builder maps method names to query parameters; it does not validate those parameters against each endpoint. Use only the filters documented for the selected resource operation. A fluent method being available does not mean every endpoint accepts it.

## Building Queries

Most resources expose `query()` as the entry point. The builder is mutable, so you may assemble it conditionally and execute it once you have applied every filter you need.

```php
use Samsara\Facades\Samsara;

$query = Samsara::vehicles()->query();

if ($tagId = request('tag_id')) {
    $query->whereTag($tagId);
}

if ($updatedAfter = request('updated_after')) {
    $query->updatedAfter($updatedAfter);
}

$vehicles = $query->limit(100)->get();
```

Some resources expose domain-specific helpers in addition to `query()`. The Vehicle Stats resource is the clearest example — `current()`, `history()`, and `feed()` each select a different endpoint, so most chains begin there rather than with the bare `query()`:

```php
$stats = Samsara::vehicleStats()
    ->current()
    ->types(['gps', 'engineStates'])
    ->whereVehicle('vehicle-id')
    ->get();
```

## Filtering

The builder exposes typed filter methods for the parameters the Samsara API accepts most often. Each method accepts a single ID or an array of IDs.

```php
$drivers = Samsara::drivers()
    ->query()
    ->whereTag(['fleet-a', 'fleet-b'])
    ->get();
```

| Method | Parameter | Description |
|---|---|---|
| `whereTag($tagIds)` | `array<string>\|string` | Filter by one or more tag IDs. |
| `whereParentTag($parentTagIds)` | `array<string>\|string` | Filter by one or more parent tag IDs. |
| `whereVehicle($vehicleIds)` | `array<string>\|string` | Filter by one or more vehicle IDs. |
| `whereDriver($driverIds)` | `array<string>\|string` | Filter by one or more driver IDs. |
| `whereAttribute($attributeValueIds)` | `array<string>\|string` | Filter by attribute value IDs. |
| `where($key, $value)` | `string`, `mixed` | Add an arbitrary query parameter for endpoints whose filters the SDK does not yet wrap. |

Dedicated list helpers serialize non-empty arrays as comma-separated values: `types()`, `expand()`, `withDecorations()`, `whereAttribute()`, `whereDriver()`, `whereParentTag()`, `whereTag()`, and `whereVehicle()`. Arbitrary arrays passed through `where()` retain Laravel's standard bracketed encoding.

Endpoint helpers use `repeatQueryParameters()` when the official operation declares a form/`explode=true` array. Those values move into the URL as repeated RFC 3986 keys (`ids=a&ids=b`). Top-level boolean query values serialize as the literal strings `true` and `false` rather than `1` and `0`. Fleet/platform repeated keys include vehicle-list and asset-list `attributes` plus asset-depreciation `assetIds`; Safety/operations uses repeated keys for alert configuration IDs, incident configuration IDs, Camera Media lists, DVIR safety status, service-task IDs, work-order IDs, and work-order-stream status/asset/assignee lists. Call `repeatQueryParameters()` directly only when building a custom endpoint whose contract requires that encoding.

## Modifiers

Modifiers shape the response — the time window, the stat types you want, and any decorations or expansions the API offers.

### Time ranges

`between()` is the convenience wrapper. It accepts strings or any `DateTimeInterface`, including Carbon instances, and sets both `startTime` and `endTime` on the query.

```php
use Carbon\Carbon;

$logs = Samsara::hoursOfService()
    ->logs()
    ->whereDriver('driver-id')
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();
```

If you need only one side of the window, call `startTime()` or `endTime()` directly. `updatedAfter()` and `createdAfter()` map to the API's `updatedAfterTime` and `createdAfterTime` parameters and accept the same input types.

```php
$drivers = Samsara::drivers()
    ->query()
    ->updatedAfter('2026-01-01T00:00:00Z')
    ->get();
```

### Stat types

For telemetry endpoints, `types()` selects which stat streams to retrieve. It accepts a single string or an array of strings.

```php
$stats = Samsara::vehicleStats()
    ->history()
    ->types(['gps', 'engineStates', 'fuelPercents'])
    ->whereVehicle('vehicle-id')
    ->between($start, $end)
    ->get();
```

> **Note:** `types()` accepts string values only. To use the [`VehicleStatType`](enums.html#vehiclestattype) enum, unwrap each case explicitly: `->types([VehicleStatType::GPS->value])`.

### Decorations

`withDecorations()` asks the API to add other stat values at the time of each primary stat.

```php
$stats = Samsara::vehicleStats()
    ->history()
    ->types(['engineStates'])
    ->withDecorations(['gps', 'fuelPercents'])
    ->between($start, $end)
    ->get();
```

### Expanding nested data

`expand()` requests inline expansion of related entities so you avoid follow-up calls.

```php
$dailyLogs = Samsara::hoursOfService()
    ->dailyLogs()
    ->where('startDate', '2026-08-01')
    ->where('endDate', '2026-08-07')
    ->expand('vehicle')
    ->get();
```

## Pagination

`limit()` caps the number of results returned by the next call. `take()` is an alias for `limit()` and exists for readability.

```php
$drivers = Samsara::drivers()
    ->query()
    ->take(50)
    ->get();
```

To start a query at a known cursor, pass the cursor token to `after()`.

```php
$page = Samsara::vehicles()
    ->query()
    ->after('cursor-token-from-previous-response')
    ->paginate(100);
```

### Cursor-Based Pagination

`paginate()` makes one request and returns a `Samsara\Query\Pagination\CursorPaginator`. The paginator is iterable, countable, and exposes the cursor metadata returned by the API. When no explicit limit exists, `paginate()` and `lazy()` apply the configured `per_page`. Explicit method arguments, `limit()`, and `where('limit', ...)` take precedence.

Some official cursor endpoints accept `after` but no `limit`. Their resource helpers call `withoutDefaultPageSize()`, so pagination sends no implicit limit. The builder still preserves an explicit caller-supplied limit, but that does not make the parameter contract-valid: omit it when the selected endpoint does not declare `limit`. Fleet/platform currently has 15 such helpers. The opt-out persists when `nextPage()` and `lazy()` clone the builder.

```php
$paginator = Samsara::vehicles()
    ->query()
    ->paginate(50);

foreach ($paginator as $vehicle) {
    echo $vehicle->name.PHP_EOL;
}

if ($paginator->hasMorePages()) {
    $next = $paginator->nextPage();
}
```

`nextPage()` returns `?CursorPaginator` — `null` when `hasMorePages()` is `false` or when the API did not return an end cursor. Always guard with `hasMorePages()` before dereferencing the result.

Samsara's current pagination guidance says to continue whenever `hasNextPage` is true, even when the current `data` array is short or empty. Cursors expire after 30 days. For `/feed` endpoints, wait at least five seconds before polling again after a response with `hasNextPage: false`.

| Method | Returns | Description |
|---|---|---|
| `items()` | `EntityCollection` | The entities on the current page. |
| `cursor()` | `Cursor` | The pagination cursor returned by the API. |
| `hasMorePages()` | `bool` | Whether another page exists. |
| `nextPage()` | `?CursorPaginator` | Fetches and returns the next page, or `null` when finished. |
| `count()` | `int` | The number of items on the current page. |
| `toArray()` | `array<string, mixed>` | A `['data' => [...], 'pagination' => [...]]` representation. |

The final `Cursor` class exposes readonly pagination properties; its former getters are removed:

| Member | Type / return | Description |
|---|---|---|
| `$cursor->endCursor` | `?string` | The cursor token for the next page. |
| `$cursor->hasNextPage` | `bool` | Whether the API reports more results. |
| `toArray()` | `array<string, mixed>` | An `['endCursor' => ..., 'hasNextPage' => ...]` representation. |

## Execution Methods

The builder defers HTTP work until you call a terminal method. Each terminal method maps to a different result shape.

| Method | Returns | When to use it |
|---|---|---|
| `get()` | `EntityCollection` | Eager fetch of the `data` array from one HTTP response. It does not walk subsequent cursors. |
| `first()` | `?Entity` | The first mapped entity, or `null`. Adds `limit(1)` only when automatic page sizing is enabled. |
| `paginate(?int $perPage = null)` | `CursorPaginator` | A page of results plus the cursor metadata for navigation. |
| `lazy(?int $chunkSize = null)` | `LazyCollection` | A memory-efficient stream that walks every page until the API stops returning a cursor. |

`first()` is null-safe — handle the absent-row case explicitly:

```php
$driver = Samsara::drivers()
    ->query()
    ->whereTag('night-shift')
    ->first();

if ($driver === null) {
    // No driver matched.
}
```

`first()` respects `withoutDefaultPageSize()`: it reads one page without adding a limit on those endpoints.

`lazy()` is the right choice for unbounded data sets such as historical telemetry. Pass a chunk size only when the selected endpoint declares `limit`; otherwise call `lazy()` with no argument.

```php
Samsara::vehicleStats()
    ->history()
    ->types(['gps'])
    ->between($start, $end)
    ->lazy()
    ->each(function ($stat) {
        // Process each stat without loading every page into memory.
    });
```

All builder terminal methods (`get()`, `first()`, `paginate()`, and every request made by `lazy()`) use the same typed HTTP error boundary as supported generic resource operations and custom helpers. The exception context records the logical endpoint path. Inspect the recorded HTTP request when you need the final encoded query string.

Endpoint builders may also select an endpoint-specific result shape through `usingEntity()` or `mapUsing()`. That mapping persists across eager, paginated, and lazy execution, which is why snapshot and time-series endpoints can return distinct DTOs instead of reusing a resource-wide entity class.

Package extenders can adapt nonstandard cursor envelopes without replacing pagination logic:

```php
return $this->newQuery('/v1/example')
    ->withResponseCollectionKey('data.rows')
    ->withCursorParameter('startingAfter');
```

`withResponseCollectionKey()` accepts dot notation for nested arrays. `withCursorParameter()` changes only the outgoing cursor name; response metadata still comes from the top-level `pagination` object. Both settings persist across `paginate()`, `nextPage()`, and `lazy()` clones.

## Working with Results

`get()` returns a `Samsara\Data\EntityCollection`, which extends Laravel's base `Collection` with two SDK-specific helpers.

```php
$drivers = Samsara::drivers()->query()->get();

$ids = $drivers->ids();
// ['driver-1', 'driver-2', ...]

$driver = $drivers->findById('driver-1');
// Driver|null
```

| Method | Returns | Description |
|---|---|---|
| `ids()` | `array<int, string>` | Every non-null, non-empty entity ID; the valid string ID `"0"` is retained. |
| `findById(string $id)` | `?Entity` | The first entity whose `getId()` matches, or `null`. |

Because `EntityCollection` is a Laravel collection, every method on the base class — `filter()`, `map()`, `pluck()`, `keyBy()`, `groupBy()`, and so on — works without modification.

`Builder` supports Laravel's `when()` and `unless()` methods. `types()` and `withDecorations()` accept a `VehicleStatType` case, strings, or arrays mixing both.

Repeated or cyclic cursors throw `SamsaraException` during `lazy()` traversal or `nextPage()` navigation. Catch that exception at the traversal boundary.

## Escape Hatch

When you need an endpoint the SDK has not yet wrapped, the underlying HTTP client is exposed via `Samsara::client()`. The returned `Illuminate\Http\Client\PendingRequest` is preconfigured with the base URL, bearer token, JSON headers, timeout, and retry settings.

```php
$response = Samsara::client()->get('/fleet/document-types');

$data = $response->json('data', []);
```

The example reads the pinned contract's document-type collection, which has no dedicated SDK resource. An application using a raw request owns its request and response contract.

Inside a custom resource subclass, the same instance is available on the resource itself via `$this->client()`, which proxies to `Samsara::client()`.

```php
namespace App\Samsara;

use Samsara\Resources\Resource;

class CustomResource extends Resource
{
    public function documentTypes(): array
    {
        return $this->client()->get('/fleet/document-types')->json('data', []);
    }
}
```

The escape hatch is also where you go for one-off requests that fall outside the typed entity model — for example, calling a beta endpoint that returns a shape the SDK does not yet model.

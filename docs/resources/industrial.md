# Industrial Resource

Manage industrial assets and data inputs for monitoring equipment and machinery.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all industrial assets
$assets = Samsara::industrial()->assets()->get();

// Create an industrial asset
$asset = Samsara::industrial()->createAsset([
    'name' => 'CNC Machine 1',
    'location' => [
        'latitude' => 37.7749,
        'longitude' => -122.4194,
    ],
]);

// Update an industrial asset
$asset = Samsara::industrial()->updateAsset('asset-id', [
    'name' => 'CNC Machine 1 - Bay A',
]);

// Delete an industrial asset
Samsara::industrial()->deleteAsset('asset-id');
```

## Data Inputs

```php
// Get data inputs stream
$dataInputs = Samsara::industrial()
    ->dataInputs()
    ->get();
```

## Data Points

```php
use Carbon\Carbon;

// Get current data points
$dataPoints = Samsara::industrial()
    ->dataPoints()
    ->get();

// Get data points feed (real-time)
$feed = Samsara::industrial()
    ->dataPointsFeed()
    ->get();

// Get historical data points
$history = Samsara::industrial()
    ->dataPointsHistory()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();
```

## Query Builder

```php
// Filter by tag
$assets = Samsara::industrial()
    ->assets()
    ->whereTag('production-floor')
    ->get();

// Limit results
$assets = Samsara::industrial()
    ->assets()
    ->limit(25)
    ->get();
```

## IndustrialAsset Entity

```php
$asset = Samsara::industrial()->assets()->first();

$asset->id;         // string
$asset->name;       // ?string
$asset->dataInputs; // ?array
$asset->location;   // ?array
$asset->tags;       // ?array
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Industrial asset ID |
| `name` | string | Asset name |
| `dataInputs` | array | Associated data inputs with id and name |
| `location` | object | Asset location with latitude/longitude |
| `tags` | array | Associated tags with id and name |

# Assets Resource

Manage fleet assets including vehicles, trailers, and equipment with tracking capabilities.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all assets
$assets = Samsara::assets()->all();

// Find an asset
$asset = Samsara::assets()->find('asset-id');

// Create an asset
$asset = Samsara::assets()->create([
    'name' => 'Forklift A',
    'assetType' => 'equipment',
    'serial' => 'FL-12345',
]);

// Update an asset
$asset = Samsara::assets()->update('asset-id', [
    'name' => 'Forklift A - Warehouse 1',
]);

// Delete an asset
Samsara::assets()->delete('asset-id');
```

## Query Builder

```php
// Filter by tag
$assets = Samsara::assets()
    ->query()
    ->whereTag('warehouse-equipment')
    ->get();

// Limit results
$assets = Samsara::assets()
    ->query()
    ->limit(50)
    ->get();
```

## Specialized Streams

```php
use Carbon\Carbon;

// Get asset depreciation data
$depreciation = Samsara::assets()
    ->depreciation()
    ->get();

// Get asset inputs stream
$inputs = Samsara::assets()
    ->inputsStream()
    ->get();

// Get asset location and speed stream
$locations = Samsara::assets()
    ->locationAndSpeedStream()
    ->between(Carbon::now()->subHours(24), Carbon::now())
    ->get();
```

## Legacy Endpoints

```php
// Get asset locations (Legacy v1)
$locations = Samsara::assets()->locations([
    'groupId' => 'group-123',
]);

// Get reefer stats (Legacy v1)
$reefers = Samsara::assets()->reefers([
    'groupId' => 'group-123',
]);
```

## Asset Entity

The `Asset` entity provides helper methods:

```php
$asset = Samsara::assets()->find('asset-id');

// Check asset type
$asset->isVehicle();    // bool
$asset->isTrailer();    // bool
$asset->isEquipment();  // bool

// Basic properties
$asset->id;            // string
$asset->name;          // ?string
$asset->serial;        // ?string
$asset->assetType;     // ?string ('vehicle', 'trailer', 'equipment')
$asset->purchasePrice; // ?float
$asset->gateway;       // ?array
$asset->location;      // ?array
$asset->tags;          // ?array
$asset->createdAtTime; // ?string
$asset->updatedAtTime; // ?string
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Asset ID |
| `name` | string | Asset name |
| `serial` | string | Serial number |
| `assetType` | string | Type ('vehicle', 'trailer', 'equipment') |
| `purchasePrice` | float | Purchase price |
| `gateway` | object | Gateway info with id and serial |
| `location` | object | Current location with latitude/longitude |
| `tags` | array | Associated tags with id and name |
| `createdAtTime` | string | Creation timestamp (RFC 3339) |
| `updatedAtTime` | string | Last update timestamp (RFC 3339) |

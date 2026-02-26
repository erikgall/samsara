---
title: Vehicles
layout: default
parent: Resources
nav_order: 2
description: "Manage vehicles in your Samsara fleet"
permalink: /resources/vehicles
---

# Vehicles Resource

Manage vehicles in your Samsara fleet.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all vehicles
$vehicles = Samsara::vehicles()->all();

// Find a vehicle
$vehicle = Samsara::vehicles()->find('vehicle-id');

// Update a vehicle
$vehicle = Samsara::vehicles()->update('vehicle-id', [
    'name' => 'Truck 001 - Updated',
]);
```

## Unsupported Operations

The Samsara API does not support creating or deleting vehicles via the `/fleet/vehicles` endpoint. Calling these methods will throw an `UnsupportedOperationException`.

### Create

Vehicles are automatically created when a Samsara Vehicle Gateway is installed. To manually create vehicles, use the Assets API.

```php
use Samsara\Facades\Samsara;
use Samsara\Exceptions\UnsupportedOperationException;

try {
    $vehicle = Samsara::vehicles()->create([
        'name' => 'Truck 001',
        'vin' => '1HGBH41JXMN109186',
    ]);
} catch (UnsupportedOperationException $e) {
    // "Vehicles cannot be created via /fleet/vehicles. Vehicles are automatically
    // created when a Samsara Vehicle Gateway is installed. To manually create
    // vehicles, use the Assets API: $samsara->assets()->create(['type' => 'vehicle', ...])."
}

// Use the Assets API instead:
$vehicle = Samsara::assets()->create([
    'type' => 'vehicle',
    'name' => 'Truck 001',
]);
```

### Delete

Vehicles cannot be deleted via the Samsara API. To retire a vehicle, update its name or notes field instead.

```php
use Samsara\Facades\Samsara;
use Samsara\Exceptions\UnsupportedOperationException;

try {
    Samsara::vehicles()->delete('vehicle-id');
} catch (UnsupportedOperationException $e) {
    // "Vehicles cannot be deleted via the Samsara API. To retire a vehicle,
    // update its name or notes field instead:
    // $samsara->vehicles()->update($id, ['name' => '[RETIRED] Vehicle Name'])."
}

// Retire a vehicle instead:
Samsara::vehicles()->update('vehicle-id', [
    'name' => '[RETIRED] Truck 001',
]);
```

## Query Builder

```php
// Filter by tag
$vehicles = Samsara::vehicles()
    ->query()
    ->whereTag('tag-id')
    ->get();

// Filter by multiple tags
$vehicles = Samsara::vehicles()
    ->query()
    ->whereTag(['tag-1', 'tag-2'])
    ->get();

// Pagination
$vehicles = Samsara::vehicles()
    ->query()
    ->limit(50)
    ->paginate();
```

## External IDs

```php
// Find by external ID
$vehicle = Samsara::vehicles()->findByExternalId('fleet_id', 'TRUCK001');
```

## Vehicle Entity

The `Vehicle` entity provides helper methods:

```php
$vehicle = Samsara::vehicles()->find('vehicle-id');

// Get display name
$vehicle->getDisplayName(); // string

// Get external ID
$vehicle->getExternalId('fleet_id'); // ?string

// Get tag IDs
$vehicle->getTagIds(); // array<string>

// Check for static assigned driver
$vehicle->hasStaticAssignedDriver(); // bool
$vehicle->getStaticAssignedDriver(); // ?StaticAssignedDriver
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Vehicle ID |
| `name` | string | Vehicle name |
| `vin` | string | VIN |
| `make` | string | Make |
| `model` | string | Model |
| `year` | string | Year |
| `licensePlate` | string | License plate |
| `serial` | string | Gateway serial |
| `notes` | string | Notes |
| `vehicleType` | string | Vehicle type |
| `externalIds` | array | External IDs |
| `tags` | array | Tags |
| `staticAssignedDriver` | array | Assigned driver |

## Related Resources

- [Vehicle Stats](vehicle-stats.md) - Telemetry data
- [Vehicle Locations](vehicle-locations.md) - GPS locations

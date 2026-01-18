# Vehicles Resource

Manage vehicles in your Samsara fleet.

## Basic Usage

```php
use ErikGall\Samsara\Facades\Samsara;

// Get all vehicles
$vehicles = Samsara::vehicles()->all();

// Find a vehicle
$vehicle = Samsara::vehicles()->find('vehicle-id');

// Create a vehicle
$vehicle = Samsara::vehicles()->create([
    'name' => 'Truck 001',
    'vin' => '1HGBH41JXMN109186',
]);

// Update a vehicle
$vehicle = Samsara::vehicles()->update('vehicle-id', [
    'name' => 'Truck 001 - Updated',
]);

// Delete a vehicle
Samsara::vehicles()->delete('vehicle-id');
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
- [Vehicle Locations](../query-builder.md) - GPS locations

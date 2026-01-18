# Addresses Resource

Manage addresses and geofences for location-based operations.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all addresses
$addresses = Samsara::addresses()->all();

// Find an address
$address = Samsara::addresses()->find('address-id');

// Create an address
$address = Samsara::addresses()->create([
    'name' => 'Distribution Center',
    'formattedAddress' => '123 Warehouse Blvd, City, ST 12345',
    'notes' => 'Main loading dock on west side',
]);

// Update an address
$address = Samsara::addresses()->update('address-id', [
    'name' => 'Main Distribution Center',
]);

// Delete an address
Samsara::addresses()->delete('address-id');
```

## Creating Geofences

```php
// Create address with circular geofence
$address = Samsara::addresses()->create([
    'name' => 'Customer Site A',
    'formattedAddress' => '456 Main St, City, ST 12345',
    'geofence' => [
        'circle' => [
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'radiusMeters' => 200,
        ],
    ],
]);

// Create address with polygon geofence
$address = Samsara::addresses()->create([
    'name' => 'Warehouse Zone',
    'formattedAddress' => '789 Industrial Ave, City, ST 12345',
    'geofence' => [
        'polygon' => [
            'vertices' => [
                ['latitude' => 37.7749, 'longitude' => -122.4194],
                ['latitude' => 37.7750, 'longitude' => -122.4180],
                ['latitude' => 37.7740, 'longitude' => -122.4180],
                ['latitude' => 37.7740, 'longitude' => -122.4194],
            ],
        ],
    ],
]);
```

## Query Builder

```php
// Filter by tag
$addresses = Samsara::addresses()
    ->query()
    ->whereTag('delivery-locations')
    ->get();

// Get addresses created after a date
$addresses = Samsara::addresses()
    ->query()
    ->createdAfter('2024-01-01T00:00:00Z')
    ->get();

// Limit results
$addresses = Samsara::addresses()
    ->query()
    ->limit(50)
    ->get();
```

## Address Entity

The `Address` entity provides helper methods:

```php
$address = Samsara::addresses()->find('address-id');

// Check geofence type
$address->hasGeofence();     // bool
$address->isCircleGeofence(); // bool
$address->isPolygonGeofence(); // bool

// Check address type
$address->isYard();      // bool
$address->isShortHaul(); // bool

// Get related data
$address->getGeofence();  // ?AddressGeofence
$address->getTagIds();    // array<string>
$address->getContactIds(); // array<string>

// Basic properties
$address->id;               // string
$address->name;             // string
$address->formattedAddress; // string
$address->latitude;         // ?float
$address->longitude;        // ?float
$address->notes;            // ?string
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Address ID |
| `name` | string | Address name |
| `formattedAddress` | string | Formatted address string |
| `latitude` | float | Latitude coordinate |
| `longitude` | float | Longitude coordinate |
| `notes` | string | Address notes |
| `geofence` | object | Geofence configuration |
| `tags` | array | Associated tags |
| `contacts` | array | Associated contacts |
| `addressTypes` | array | Address type classifications |
| `externalIds` | array | External ID mappings |

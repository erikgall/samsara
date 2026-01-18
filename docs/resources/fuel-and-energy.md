# Fuel and Energy Resource

Access fuel and energy efficiency data and reports.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get driver fuel and energy report
$driverReport = Samsara::fuelAndEnergy()->driversFuelEnergyReport([
    'startTime' => '2024-01-01T00:00:00Z',
    'endTime' => '2024-01-31T23:59:59Z',
]);

// Get vehicle fuel and energy report
$vehicleReport = Samsara::fuelAndEnergy()->vehiclesFuelEnergyReport([
    'startTime' => '2024-01-01T00:00:00Z',
    'endTime' => '2024-01-31T23:59:59Z',
]);
```

## Efficiency Streams

```php
use Carbon\Carbon;

// Get driver efficiency stream
$driverEfficiency = Samsara::fuelAndEnergy()
    ->driverEfficiency()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();

// Get vehicle efficiency stream
$vehicleEfficiency = Samsara::fuelAndEnergy()
    ->vehicleEfficiency()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();
```

## Fuel Purchases

```php
// Create a fuel purchase record
$purchase = Samsara::fuelAndEnergy()->createFuelPurchase([
    'vehicleId' => 'vehicle-123',
    'driverId' => 'driver-456',
    'fuelType' => 'diesel',
    'volumeGallons' => 50.5,
    'pricePerGallon' => 3.89,
    'totalAmount' => 196.45,
    'purchaseTime' => '2024-01-15T14:30:00Z',
    'location' => 'Truck Stop A, Highway 101',
]);
```

## Query Builder

```php
// Filter efficiency by vehicle
$vehicleEfficiency = Samsara::fuelAndEnergy()
    ->vehicleEfficiency()
    ->whereVehicle('vehicle-123')
    ->between(now()->subDays(30), now())
    ->get();

// Filter efficiency by tag
$driverEfficiency = Samsara::fuelAndEnergy()
    ->driverEfficiency()
    ->whereTag('long-haul-fleet')
    ->between(now()->subDays(30), now())
    ->get();
```

## Report Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `startTime` | string | Report start time (ISO 8601) |
| `endTime` | string | Report end time (ISO 8601) |
| `tagIds` | array | Filter by tag IDs |
| `vehicleIds` | array | Filter by vehicle IDs |
| `driverIds` | array | Filter by driver IDs |

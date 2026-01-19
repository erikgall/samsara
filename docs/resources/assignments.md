---
title: Assignments
layout: default
parent: Resources
nav_order: 34
description: "Manage driver, vehicle, and trailer assignments"
permalink: /resources/assignments
---

# Assignments Resources

Manage various assignment types for drivers, vehicles, and trailers.

## Driver-Vehicle Assignments

```php
use Samsara\Facades\Samsara;

// Get driver-vehicle assignments
$assignments = Samsara::driverVehicleAssignments()
    ->query()
    ->get();

// Filter by driver
$assignments = Samsara::driverVehicleAssignments()
    ->query()
    ->whereDriver('driver-123')
    ->get();

// Filter by vehicle
$assignments = Samsara::driverVehicleAssignments()
    ->query()
    ->whereVehicle('vehicle-456')
    ->get();
```

## Driver-Trailer Assignments

```php
// Get driver-trailer assignments
$assignments = Samsara::driverTrailerAssignments()
    ->query()
    ->get();

// Filter by driver
$assignments = Samsara::driverTrailerAssignments()
    ->query()
    ->whereDriver('driver-123')
    ->get();
```

## Trailer Assignments (Legacy)

```php
// Get assignments for a specific trailer
$assignments = Samsara::trailerAssignments()
    ->forTrailer('trailer-123');
```

## Carrier Proposed Assignments

```php
// Get all carrier proposed assignments
$assignments = Samsara::carrierProposedAssignments()->all();

// Find a carrier proposed assignment
$assignment = Samsara::carrierProposedAssignments()->find('assignment-id');

// Create a carrier proposed assignment
$assignment = Samsara::carrierProposedAssignments()->create([
    'driverId' => 'driver-123',
    'vehicleId' => 'vehicle-456',
    'startTime' => '2024-01-15T08:00:00Z',
]);

// Update a carrier proposed assignment
$assignment = Samsara::carrierProposedAssignments()->update('assignment-id', [
    'endTime' => '2024-01-15T18:00:00Z',
]);

// Delete a carrier proposed assignment
Samsara::carrierProposedAssignments()->delete('assignment-id');
```

---
title: Drivers
layout: default
parent: Resources
nav_order: 1
description: "Manage drivers in your Samsara fleet"
permalink: /resources/drivers
---

# Drivers Resource

Manage drivers in your Samsara fleet.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all drivers
$drivers = Samsara::drivers()->all();

// Find a driver
$driver = Samsara::drivers()->find('driver-id');

// Create a driver
$driver = Samsara::drivers()->create([
    'name' => 'John Doe',
    'phone' => '+1234567890',
]);

// Update a driver
$driver = Samsara::drivers()->update('driver-id', [
    'phone' => '+0987654321',
]);

// Delete a driver
Samsara::drivers()->delete('driver-id');
```

## Query Builder

```php
// Filter active drivers
$activeDrivers = Samsara::drivers()->active()->get();

// Filter deactivated drivers
$deactivatedDrivers = Samsara::drivers()->deactivated()->get();

// Filter by tag
$drivers = Samsara::drivers()
    ->query()
    ->whereTag('tag-id')
    ->get();

// Filter by parent tag
$drivers = Samsara::drivers()
    ->query()
    ->whereParentTag('parent-tag-id')
    ->get();
```

## Driver Activation

```php
// Deactivate a driver
$driver = Samsara::drivers()->deactivate('driver-id');

// Activate a driver
$driver = Samsara::drivers()->activate('driver-id');
```

## External IDs

```php
// Find by external ID
$driver = Samsara::drivers()->findByExternalId('payroll_id', '12345');
```

## Remote Sign Out

```php
// Remotely sign out a driver from their device
Samsara::drivers()->remoteSignOut('driver-id');
```

## QR Codes

```php
// Get all QR codes
$qrCodes = Samsara::drivers()->getQrCodes();

// Create a QR code
$qrCode = Samsara::drivers()->createQrCode([
    'driverId' => 'driver-id',
]);

// Delete a QR code
Samsara::drivers()->deleteQrCode('qr-code-id');
```

## Auth Tokens

```php
// Create an authentication token for a driver
$token = Samsara::drivers()->createAuthToken('driver-id');
```

## Driver Entity

The `Driver` entity provides helper methods:

```php
$driver = Samsara::drivers()->find('driver-id');

// Check status
$driver->isActive();      // bool
$driver->isDeactivated(); // bool

// Get display name (name or username)
$driver->getDisplayName(); // string

// Check ELD settings
$driver->isEldExempt();              // bool
$driver->isPersonalConveyanceEnabled(); // bool
$driver->isYardMoveEnabled();        // bool

// Get related entities
$driver->getEldSettings();         // ?EldSettings
$driver->getCarrierSettings();     // ?CarrierSettings
$driver->getStaticAssignedVehicle(); // ?StaticAssignedVehicle

// Get external ID
$driver->getExternalId('payroll_id'); // ?string

// Get tag IDs
$driver->getTagIds(); // array<string>
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Driver ID |
| `name` | string | Driver name |
| `username` | string | Login username |
| `phone` | string | Phone number |
| `licenseNumber` | string | License number |
| `licenseState` | string | License state |
| `timezone` | string | Timezone |
| `driverActivationStatus` | string | 'active' or 'deactivated' |
| `eldExempt` | bool | ELD exempt flag |
| `eldPcEnabled` | bool | Personal conveyance enabled |
| `eldYmEnabled` | bool | Yard move enabled |
| `externalIds` | array | External ID mappings |
| `tags` | array | Associated tags |

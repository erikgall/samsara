---
title: Drivers
nav_order: 1
description: Manage drivers in your Samsara fleet.
permalink: /resources/drivers
---

# Drivers

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
  - [Active and Deactivated](#active-and-deactivated)
  - [By External ID](#by-external-id)
- [Additional Operations](#additional-operations)
  - [Activation and Deactivation](#activation-and-deactivation)
  - [Remote Sign Out](#remote-sign-out)
  - [QR Codes](#qr-codes)
  - [Auth Tokens](#auth-tokens)
- [Driver Activation Status](#driver-activation-status)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Drivers are the people who operate vehicles in your Samsara fleet. Reach for this resource when you need to maintain the driver roster, manage activation, look drivers up by an external payroll ID, or generate the credentials a driver uses to log into the Samsara app.

Use `query()` for collection reads. The resource exposes `find`, `create`, and `update`; `all` and `delete` are removed. The custom auth-token, QR-code, and remote-sign-out helpers use the current paths and typed return objects described below.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$drivers = Samsara::drivers()->query()->get();

$driver = Samsara::drivers()->find('driver-id');
```

## Creating Records

```php
$driver = Samsara::drivers()->create([
    'name' => 'John Doe',
    'username' => 'john.doe',
    'password' => $driverPassword,
    'phone' => '+1234567890',
]);
```

## Updating Records

```php
$driver = Samsara::drivers()->update('driver-id', [
    'phone' => '+0987654321',
]);
```

## Deleting Records

`delete()` is removed. Use `deactivate()` to deactivate a driver.

## Filtering

The list operation accepts activation status, pagination, tag, attribute, update-time, and create-time filters. See [the query builder reference](../query-builder.html) for the fluent method mapping.

```php
$drivers = Samsara::drivers()
    ->query()
    ->whereTag('tag-id')
    ->whereParentTag('parent-tag-id')
    ->get();
```

### Active and Deactivated

The resource ships two convenience sub-builders that pre-filter on `driverActivationStatus`.

```php
$activeDrivers = Samsara::drivers()->active()->get();

$deactivatedDrivers = Samsara::drivers()->deactivated()->get();
```

### By External ID

`findByExternalId()` looks up a single driver using the item identifier form `{key}:{value}`.

```php
$driver = Samsara::drivers()->findByExternalId('payroll_id', '12345');
```

## Additional Operations

| Method | Request | Return |
|--------|---------|--------|
| `createAuthToken(array $data)` | `POST /fleet/drivers/auth-token` | `DriverAuthToken` (`token`, `expirationTime`) |
| `createQrCode(array $data)` | `POST /drivers/qr-codes` | `DriverQrCode` |
| `getQrCodes(array\|string $driverIds)` | `GET /drivers/qr-codes?driverIds=...` | `EntityCollection<int, DriverQrCode>` |
| `deleteQrCode(int $driverId)` | `DELETE /drivers/qr-codes` with `driverId` in the body | `bool` |
| `remoteSignOut(string $id)` | `POST /fleet/drivers/remote-sign-out` with `driverId` in the body | `DriverRemoteSignOutResult` |

### Activation and Deactivation

Both helpers wrap an `update()` call that flips `driverActivationStatus`.

```php
$driver = Samsara::drivers()->deactivate('driver-id');

$driver = Samsara::drivers()->activate('driver-id');
```

### Remote Sign Out

```php
$result = Samsara::drivers()->remoteSignOut('driver-id');
$result->driverName; // ?string
```

### QR Codes

QR codes provide a passwordless app login path for drivers.

```php
$qrCodes = Samsara::drivers()->getQrCodes(['123', '456']);

$qrCode = Samsara::drivers()->createQrCode([
    'driverId' => 494123,
]);

Samsara::drivers()->deleteQrCode(123);
```

### Auth Tokens

`createAuthToken()` accepts the official payload and returns a `DriverAuthToken` rather than a bare token string.

```php
$auth = Samsara::drivers()->createAuthToken([
    'code' => 'driver-code-1234',
    'driverId' => 494123,
]);

$token = $auth->token;
```

`code` is required and must contain at least 12 characters. Identify the driver with an integer `driverId`, an `externalId` formatted as `key:value`, or a `username`. The separate Preview auth-token endpoint uses `id`; do not copy that Preview-only key into this production endpoint.

## Driver Activation Status

The `Samsara\Enums\DriverActivationStatus` enum lists the values for the `driverActivationStatus` field. See the [enum reference](../enums.html) for the canonical mapping.

| Case | Value | Description |
|------|-------|-------------|
| `ACTIVE` | `active` | Driver can log into the app and be assigned to a vehicle. |
| `DEACTIVATED` | `deactivated` | Driver is archived but their history is retained. |

The `Driver` entity exposes a typed accessor:

```php
$driver->getActivationStatus(); // ?DriverActivationStatus
```

## Helper Methods

```php
$driver = Samsara::drivers()->find('driver-id');

$driver->isActive();                    // bool
$driver->isDeactivated();               // bool

$driver->getDisplayName();              // string — name, falls back to username

$driver->isEldExempt();                 // bool
$driver->isPersonalConveyanceEnabled(); // bool
$driver->isYardMoveEnabled();           // bool

$driver->getEldSettings();              // ?EldSettings
$driver->getCarrierSettings();          // ?CarrierSettings
$driver->getStaticAssignedVehicle();    // ?StaticAssignedVehicle
$driver->hasStaticAssignedVehicle();    // bool

$driver->getExternalId('payroll_id');   // ?string
$driver->getTagIds();                   // array<int, string>
```

## Properties

The `Driver` entity (`Samsara\Data\Driver\Driver`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Driver ID. |
| `name` | `?string` | Driver name. |
| `username` | `?string` | Login username. |
| `phone` | `?string` | Phone number. |
| `licenseNumber` | `?string` | Driver's license number. |
| `licenseState` | `?string` | License state abbreviation. |
| `timezone` | `?string` | Home terminal timezone. |
| `notes` | `?string` | Notes about the driver. |
| `createdAtTime` | `?string` | Creation timestamp (RFC 3339). |
| `updatedAtTime` | `?string` | Last update timestamp (RFC 3339). |
| `driverActivationStatus` | `?string` | `active` or `deactivated`. See [`DriverActivationStatus`](#driver-activation-status). |
| `profileImageUrl` | `?string` | Profile image URL. |
| `locale` | `?string` | Driver's locale. |
| `currentIdCardCode` | `?string` | Current ID card code. |
| `tachographCardNumber` | `?string` | Tachograph card number. |
| `eldExempt` | `?bool` | Whether the driver is ELD exempt. |
| `eldExemptReason` | `?string` | Reason text when `eldExempt` is true. |
| `eldPcEnabled` | `?bool` | Personal conveyance enabled. |
| `eldYmEnabled` | `?bool` | Yard move enabled. |
| `externalIds` | `?array<string, string>` | External ID mappings. |
| `tags` | `?array` | Associated tags. Each entry is `{id, name?, parentTagId?}`. |
| `eldSettings` | `?array` | ELD settings — `{rulesets?: [...]}`. Use `getEldSettings()` for a typed wrapper. |
| `carrierSettings` | `?array` | Carrier settings. Use `getCarrierSettings()` for a typed wrapper. |
| `staticAssignedVehicle` | `?array` | Static assigned vehicle — `{id, name?}`. Use `getStaticAssignedVehicle()` for a typed wrapper. |

## Related Resources

- [Hours of Service](hours-of-service.html) — HOS logs, clocks, and violations per driver.
- [Assignments](assignments.html) — driver-vehicle and driver-trailer assignment history.
- [Tags](tags.html) — group drivers for filtering.
- [Query Builder](../query-builder.html) — filters and pagination.
- [Testing](../testing.html) — fake `Samsara::drivers()` calls in your tests.

---
title: Fuel and Energy
nav_order: 30
description: Pull fuel and energy efficiency reports and stream efficiency data.
permalink: /resources/fuel-and-energy
---

# Fuel and Energy

- [Introduction](#introduction)
- [Fuel and Energy Reports](#fuel-and-energy-reports)
- [Efficiency Streams](#efficiency-streams)
- [Recording Fuel Purchases](#recording-fuel-purchases)
- [Filtering](#filtering)
- [Report Parameters](#report-parameters)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Fuel and Energy aggregates the fuel use, energy consumption, and efficiency metrics Samsara computes from vehicle telemetry and fuel-card transactions. Reach for this resource when you need to feed mileage and fuel data into reporting, or to record purchases that did not flow through a connected fuel card. Every method on this resource is time-bounded — you must always pass a start and end window.

## Fuel and Energy Reports

Reports return a raw `array<string, mixed>` payload from the API. Use them when you want a per-driver or per-vehicle rollup over a date range.

```php
use Samsara\Facades\Samsara;

$driverReport = Samsara::fuelAndEnergy()->driversFuelEnergyReport([
    'startTime' => '2024-01-01T00:00:00Z',
    'endTime'   => '2024-01-31T23:59:59Z',
]);

$vehicleReport = Samsara::fuelAndEnergy()->vehiclesFuelEnergyReport([
    'startTime' => '2024-01-01T00:00:00Z',
    'endTime'   => '2024-01-31T23:59:59Z',
]);
```

## Efficiency Streams

The efficiency endpoints expose a query builder that streams events between two timestamps. `between()` is required.

```php
use Carbon\Carbon;

$driverEfficiency = Samsara::fuelAndEnergy()
    ->driverEfficiency()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();

$vehicleEfficiency = Samsara::fuelAndEnergy()
    ->vehicleEfficiency()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();
```

Stream rows come back as generic `Entity` instances — read fields with `$row->get('vehicle')`, `$row->get('efficiencyMpge')`, and so on.

## Recording Fuel Purchases

When a fuel transaction did not flow through a connected card, you can record it manually:

```php
$purchase = Samsara::fuelAndEnergy()->createFuelPurchase([
    'vehicleId'      => 'vehicle-123',
    'driverId'       => 'driver-456',
    'fuelType'       => 'diesel',
    'volumeGallons'  => 50.5,
    'pricePerGallon' => 3.89,
    'totalAmount'    => 196.45,
    'purchaseTime'   => '2024-01-15T14:30:00Z',
    'location'       => 'Truck Stop A, Highway 101',
]);
```

`createFuelPurchase()` returns the raw response array.

## Filtering

The efficiency builders support the standard filters from the query builder. See [the query builder reference](../query-builder.md) for the full method list. `between()` is still required.

```php
$vehicleEfficiency = Samsara::fuelAndEnergy()
    ->vehicleEfficiency()
    ->whereVehicle('vehicle-123')
    ->between(now()->subDays(30), now())
    ->get();

$driverEfficiency = Samsara::fuelAndEnergy()
    ->driverEfficiency()
    ->whereTag('long-haul-fleet')
    ->between(now()->subDays(30), now())
    ->get();
```

## Report Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `startTime` | `string` | Report start time (RFC 3339). Required. |
| `endTime` | `string` | Report end time (RFC 3339). Required. |
| `tagIds` | `array<int, string>` | Filter by tag IDs. |
| `vehicleIds` | `array<int, string>` | Filter by vehicle IDs. |
| `driverIds` | `array<int, string>` | Filter by driver IDs. |

## Properties

This resource does not declare a typed entity. Reports and `createFuelPurchase()` return raw `array<string, mixed>` payloads from the API; the efficiency streams return generic `Samsara\Data\Entity` instances.

## Related Resources

- [Vehicles](vehicles.md) — vehicle records referenced from efficiency rows.
- [Drivers](drivers.md) — driver records referenced from efficiency rows.
- [IFTA](ifta.md) — IFTA mileage and fuel tax reporting.
- [Query Builder](../query-builder.md) — `between()` and time-bounded queries.

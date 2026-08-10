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

Fuel and Energy aggregates the fuel use, energy consumption, and efficiency metrics Samsara computes from vehicle telemetry and fuel-card transactions. Report and efficiency methods are time-bounded; `createFuelPurchase()` is a mutation rather than a time-window query.

The explicit helpers use `/driver-efficiency/drivers`, `/driver-efficiency/vehicles`, `/fleet/reports/drivers/fuel-energy`, `/fleet/reports/vehicles/fuel-energy`, and singular `/fuel-purchase`. Generic CRUD is unsupported.

## Fuel and Energy Reports

Reports return a raw `array<string, mixed>` payload from the API. Use them when you want a per-driver or per-vehicle rollup over a date range.

```php
use Samsara\Facades\Samsara;

$driverReport = Samsara::fuelAndEnergy()->driversFuelEnergyReport([
    'startDate' => '2026-01-01',
    'endDate'   => '2026-01-31',
]);

$vehicleReport = Samsara::fuelAndEnergy()->vehiclesFuelEnergyReport([
    'startDate' => '2026-01-01',
    'endDate'   => '2026-01-31',
]);
```

## Efficiency Streams

The efficiency endpoints expose query builders whose date window uses `startDate` and `endDate`.

```php
use Carbon\Carbon;

$driverEfficiency = Samsara::fuelAndEnergy()
    ->driverEfficiency()
    ->where('startDate', Carbon::now()->subDays(30)->toDateString())
    ->where('endDate', Carbon::now()->toDateString())
    ->get();

$vehicleEfficiency = Samsara::fuelAndEnergy()
    ->vehicleEfficiency()
    ->where('startDate', Carbon::now()->subDays(30)->toDateString())
    ->where('endDate', Carbon::now()->toDateString())
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

The efficiency builders support the standard filters from the query builder. See [the query builder reference](../query-builder.html) for the full method list. Supply `startDate` and `endDate` with `where()`.

```php
$vehicleEfficiency = Samsara::fuelAndEnergy()
    ->vehicleEfficiency()
    ->whereVehicle('vehicle-123')
    ->where('startDate', now()->subDays(30)->toDateString())
    ->where('endDate', now()->toDateString())
    ->get();

$driverEfficiency = Samsara::fuelAndEnergy()
    ->driverEfficiency()
    ->whereTag('long-haul-fleet')
    ->where('startDate', now()->subDays(30)->toDateString())
    ->where('endDate', now()->toDateString())
    ->get();
```

## Report Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `startDate` | `string` | Report start date. Required. |
| `endDate` | `string` | Report end date. Required. |
| `tagIds` | `array<int, string>` | Filter by tag IDs. |
| `vehicleIds` | `array<int, string>` | Filter by vehicle IDs. |
| `driverIds` | `array<int, string>` | Filter by driver IDs. |

## Properties

This resource does not declare a typed entity. Reports and `createFuelPurchase()` return raw `array<string, mixed>` payloads from the API; the efficiency streams return generic `Samsara\Data\Entity` instances.

## Related Resources

- [Vehicles](vehicles.html) — vehicle records referenced from efficiency rows.
- [Drivers](drivers.html) — driver records referenced from efficiency rows.
- [IFTA](ifta.html) — IFTA mileage and fuel tax reporting.
- [Query Builder](../query-builder.html) — `between()` and time-bounded queries.

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

The efficiency endpoints expose query builders whose timestamp window uses `startTime` and `endTime`.

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
    'vehicleId' => 'vehicle-123',
    'driverId' => 'driver-456',
    'iftaFuelType' => 'Diesel',
    'fuelQuantityLiters' => '189.27',
    'transactionLocation' => '1 De Haro St, San Francisco, CA 94107',
    'transactionPrice' => [
        'amount' => '196.45',
        'currency' => 'usd',
    ],
    'transactionReference' => 'INV-12345',
    'transactionTime' => '2026-01-15T14:30:00Z',
]);
```

`fuelQuantityLiters`, `transactionLocation`, `transactionPrice`, `transactionReference`, and `transactionTime` are required. Convert gallon-based source data to liters; `transactionPrice.amount` is the total transaction amount, not a per-gallon scalar. `createFuelPurchase()` returns the unwrapped response data when present.

## Filtering

The efficiency builders accept their matching driver/vehicle IDs, tags, parent tags, data formats, cursors, and the required `startTime`/`endTime` window. Report helpers separately require `startDate`/`endDate`. See [the query builder reference](../query-builder.html) for the fluent method mapping.

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
| `startDate` | `string` | Report start date. Required. |
| `endDate` | `string` | Report end date. Required. |
| `tagIds` | comma-separated `string` | Filter by tag IDs, such as `tag-1,tag-2`. |
| `parentTagIds` | comma-separated `string` | Filter by parent tag IDs. |
| `vehicleIds` | comma-separated `string` | Vehicle-report filter, including external IDs when documented. |
| `driverIds` | comma-separated `string` | Driver-report filter, including external IDs when documented. |

The raw report helpers forward `$params` unchanged. Join multi-ID filters before calling them; a PHP array would use bracket encoding and violate these report contracts.

## Properties

This resource does not declare a typed entity. Reports and `createFuelPurchase()` return raw `array<string, mixed>` payloads from the API; the efficiency streams return generic `Samsara\Data\Entity` instances.

## Related Resources

- [Vehicles](vehicles.html) — vehicle records referenced from efficiency rows.
- [Drivers](drivers.html) — driver records referenced from efficiency rows.
- [IFTA](ifta.html) — IFTA mileage and fuel tax reporting.
- [Query Builder](../query-builder.html) — `between()` and time-bounded queries.

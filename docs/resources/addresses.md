---
title: Addresses
nav_order: 12
description: Manage addresses and geofences for location-based fleet operations.
permalink: /resources/addresses
---

# Addresses

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
  - [Circular Geofences](#circular-geofences)
  - [Polygon Geofences](#polygon-geofences)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

An address in Samsara is a named location with an optional **geofence** — a polygon or circle that encloses an area you care about, such as a yard, customer site, or distribution center. The Samsara platform uses geofences to detect arrivals, departures, and dwell time. Reach for this resource when you need to maintain that directory of geofenced locations and the contacts attached to them.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$addresses = Samsara::addresses()->all();

$address = Samsara::addresses()->find('address-id');
```

## Creating Records

```php
$address = Samsara::addresses()->create([
    'name'             => 'Distribution Center',
    'formattedAddress' => '123 Warehouse Blvd, City, ST 12345',
    'notes'            => 'Main loading dock on west side',
]);
```

### Circular Geofences

Pass a `circle` payload with center coordinates and a radius in meters.

```php
$address = Samsara::addresses()->create([
    'name'             => 'Customer Site A',
    'formattedAddress' => '456 Main St, City, ST 12345',
    'geofence' => [
        'circle' => [
            'latitude'     => 37.7749,
            'longitude'    => -122.4194,
            'radiusMeters' => 200,
        ],
    ],
]);
```

### Polygon Geofences

Pass a `polygon` payload with three or more vertices.

```php
$address = Samsara::addresses()->create([
    'name'             => 'Warehouse Zone',
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

A geofence is either a circle or a polygon, never both.

## Updating Records

```php
$address = Samsara::addresses()->update('address-id', [
    'name' => 'Main Distribution Center',
]);
```

## Deleting Records

```php
Samsara::addresses()->delete('address-id');
```

## Filtering

Addresses accept the standard query builder. See [the query builder reference](../query-builder.md) for the full method list.

```php
$addresses = Samsara::addresses()
    ->query()
    ->whereTag('delivery-locations')
    ->createdAfter('2024-01-01T00:00:00Z')
    ->limit(50)
    ->get();
```

## Helper Methods

The `Address` entity exposes helpers for inspecting the geofence and the related entities attached to it.

```php
$address = Samsara::addresses()->find('address-id');

$address->hasGeofence();        // bool
$address->isCircleGeofence();   // bool
$address->isPolygonGeofence();  // bool

$address->isYard();             // bool — checks `addressTypes` for 'yard'
$address->isShortHaul();        // bool — checks `addressTypes` for 'shortHaul'
$address->hasAddressType('customer'); // bool — generic check

$address->getGeofence();        // ?AddressGeofence
$address->getTagIds();          // array<int, string>
$address->getContactIds();      // array<int, string>
```

`getGeofence()` returns an `AddressGeofence` entity with its own helpers — `getCenter()`, `getRadius()`, `getVertices()`, `isCircle()`, and `isPolygon()`.

## Properties

The `Address` entity (`Samsara\Data\Address\Address`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Address ID. |
| `name` | `?string` | Address name. |
| `formattedAddress` | `?string` | Full street address. |
| `latitude` | `?float` | Latitude coordinate. |
| `longitude` | `?float` | Longitude coordinate. |
| `notes` | `?string` | Notes about the address. |
| `createdAtTime` | `?string` | Creation timestamp (RFC 3339). |
| `addressTypes` | `?array<int, string>` | Address type classifications (e.g., `yard`, `shortHaul`). |
| `externalIds` | `?array<string, string>` | External ID mappings. |
| `contacts` | `?array` | Associated contacts. Each entry is `{id, firstName?, lastName?}`. |
| `tags` | `?array` | Associated tags. Each entry is `{id, name?, parentTagId?}`. |
| `geofence` | `?array` | Raw geofence payload. Use `getGeofence()` for a typed wrapper. |

## Related Resources

- [Contacts](contacts.md) — attach a contact directory to your addresses.
- [Tags](tags.md) — group addresses for filtering.
- [Routes](routes.md) — addresses are referenced by route stops.
- [Query Builder](../query-builder.md) — for filtering, pagination, and lazy iteration.

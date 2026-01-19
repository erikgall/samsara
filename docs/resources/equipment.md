---
title: Equipment
layout: default
parent: Resources
nav_order: 4
description: "Manage and track equipment assets in your Samsara fleet"
permalink: /resources/equipment
---

# Equipment Resource

Manage and track equipment assets in your Samsara fleet.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all equipment
$equipment = Samsara::equipment()->all();

// Find equipment
$item = Samsara::equipment()->find('equipment-id');

// Create equipment
$item = Samsara::equipment()->create([
    'name' => 'Generator G-100',
    'serialNumber' => 'GEN12345',
]);

// Update equipment
$item = Samsara::equipment()->update('equipment-id', [
    'name' => 'Generator G-100 Updated',
]);

// Delete equipment
Samsara::equipment()->delete('equipment-id');
```

## Query Builder

```php
// Filter by tag
$equipment = Samsara::equipment()
    ->query()
    ->whereTag('tag-id')
    ->get();

// Filter by parent tag
$equipment = Samsara::equipment()
    ->query()
    ->whereParentTag('parent-tag-id')
    ->get();
```

## External IDs

```php
// Find by external ID
$item = Samsara::equipment()->findByExternalId('asset_id', 'EQ-12345');
```

## Equipment Locations

```php
// Get current equipment locations
$locations = Samsara::equipment()->locations()->get();

// Get equipment locations feed (for polling)
$feed = Samsara::equipment()->locationsFeed()->get();

// Get equipment locations history
$history = Samsara::equipment()
    ->locationsHistory()
    ->between(now()->subDays(7), now())
    ->get();
```

## Equipment Stats

```php
// Get current equipment stats
$stats = Samsara::equipment()->stats()->get();

// Get equipment stats feed (for polling)
$feed = Samsara::equipment()->statsFeed()->get();

// Get equipment stats history
$history = Samsara::equipment()
    ->statsHistory()
    ->between(now()->subDays(7), now())
    ->get();
```

## Equipment Entity

```php
$item = Samsara::equipment()->find('equipment-id');

$item->id;           // string
$item->name;         // string
$item->serialNumber; // ?string
$item->notes;        // ?string
$item->tags;         // array
$item->externalIds;  // array
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Equipment ID |
| `name` | string | Equipment name |
| `serialNumber` | string | Serial number |
| `notes` | string | Notes |
| `tags` | array | Associated tags |
| `externalIds` | array | External ID mappings |

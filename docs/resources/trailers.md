---
title: Trailers
layout: default
parent: Resources
nav_order: 3
description: "Manage trailers in your Samsara fleet"
permalink: /resources/trailers
---

# Trailers Resource

Manage trailers in your Samsara fleet.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all trailers
$trailers = Samsara::trailers()->all();

// Find a trailer
$trailer = Samsara::trailers()->find('trailer-id');

// Create a trailer
$trailer = Samsara::trailers()->create([
    'name' => 'Trailer T-500',
    'serialNumber' => 'SN12345678',
]);

// Update a trailer
$trailer = Samsara::trailers()->update('trailer-id', [
    'name' => 'Trailer T-500 Updated',
]);

// Delete a trailer
Samsara::trailers()->delete('trailer-id');
```

## Query Builder

```php
// Filter by tag
$trailers = Samsara::trailers()
    ->query()
    ->whereTag('tag-id')
    ->get();

// Filter by parent tag
$trailers = Samsara::trailers()
    ->query()
    ->whereParentTag('parent-tag-id')
    ->get();

// Limit results
$trailers = Samsara::trailers()
    ->query()
    ->limit(10)
    ->get();
```

## External IDs

```php
// Find by external ID
$trailer = Samsara::trailers()->findByExternalId('asset_id', 'TRL-12345');
```

## Trailer Entity

```php
$trailer = Samsara::trailers()->find('trailer-id');

$trailer->id;           // string
$trailer->name;         // string
$trailer->serialNumber; // ?string
$trailer->notes;        // ?string
$trailer->tags;         // array
$trailer->externalIds;  // array
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Trailer ID |
| `name` | string | Trailer name |
| `serialNumber` | string | Serial number |
| `notes` | string | Notes |
| `tags` | array | Associated tags |
| `externalIds` | array | External ID mappings |

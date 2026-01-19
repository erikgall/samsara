---
title: Hubs
layout: default
parent: Resources
nav_order: 32
description: "Manage hubs for organizing fleet operations"
permalink: /resources/hubs
---

# Hubs Resource

Manage hubs for organizing fleet operations.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all hubs
$hubs = Samsara::hubs()->all();

// Find a hub
$hub = Samsara::hubs()->find('hub-id');

// Create a hub
$hub = Samsara::hubs()->create([
    'name' => 'West Coast Hub',
]);

// Update a hub
$hub = Samsara::hubs()->update('hub-id', [
    'name' => 'West Coast Operations Hub',
]);

// Delete a hub
Samsara::hubs()->delete('hub-id');
```

## Query Builder

```php
// Get all hubs with query builder
$hubs = Samsara::hubs()
    ->query()
    ->get();

// Limit results
$hubs = Samsara::hubs()
    ->query()
    ->limit(25)
    ->get();
```

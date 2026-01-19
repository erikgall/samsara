---
title: Alerts
layout: default
parent: Resources
nav_order: 22
description: "Manage alert configurations and access alert incidents"
permalink: /resources/alerts
---

# Alerts Resource

Manage alert configurations and access alert incidents.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all alert configurations
$configurations = Samsara::alerts()->configurations()->get();

// Create an alert configuration
$config = Samsara::alerts()->createConfiguration([
    'name' => 'Speeding Alert',
    'type' => 'speeding',
    'threshold' => 75,
    'tagIds' => ['tag-123'],
]);

// Update an alert configuration
$config = Samsara::alerts()->updateConfiguration('config-id', [
    'name' => 'Updated Speeding Alert',
    'threshold' => 80,
]);

// Delete alert configurations
Samsara::alerts()->deleteConfigurations(['config-id-1', 'config-id-2']);
```

## Alert Incidents

```php
// Get alert incidents stream
$incidents = Samsara::alerts()->incidents()->get();

// Filter incidents by tag
$incidents = Samsara::alerts()
    ->incidents()
    ->whereTag('monitored-fleet')
    ->get();

// Filter incidents by time range
$incidents = Samsara::alerts()
    ->incidents()
    ->between(now()->subDays(7), now())
    ->get();
```

## Query Builder

```php
// Filter configurations by tag
$configurations = Samsara::alerts()
    ->configurations()
    ->whereTag('safety-alerts')
    ->get();

// Limit results
$configurations = Samsara::alerts()
    ->configurations()
    ->limit(25)
    ->get();
```

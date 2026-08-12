---
title: Live Shares
nav_order: 18
description: Time-limited share links that expose vehicle locations to non-Samsara users.
permalink: /resources/live-shares
---

# Live Shares

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Common Use Cases](#common-use-cases)
- [Related Resources](#related-resources)

## Introduction

The `Samsara::liveShares()` resource manages live sharing links — time-limited URLs that let people without Samsara accounts watch a vehicle on a map. Each share has an expiration time, a list of shared assets, and a list of email recipients; revoke it by deleting the live share record.

The resource owns generic `all`, `create`, `update`, and `delete`. `update($id, $data)` and `delete($id)` use the collection path `/live-shares?id={id}`. Generic `find` is unsupported and throws before HTTP.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$shares = Samsara::liveShares()->all();

$active = Samsara::liveShares()
    ->query()
    ->limit(25)
    ->get();
```

`all()` returns an `EntityCollection<int, LiveShare>`. Use `query()` to filter the collection; there is no single-record `find()` capability.

## Creating Records

```php
$share = Samsara::liveShares()->create([
    'name' => 'Customer Fleet View',
    'assets' => [
        ['id' => 'vehicle-id', 'type' => 'vehicle'],
    ],
    'recipients' => [
        ['email' => 'customer@example.com'],
    ],
    'expiresAt' => now()->addDay()->toIso8601String(),
]);
```

`create()` returns the saved `LiveShare` entity.

## Updating Records

```php
$share = Samsara::liveShares()->update('live-share-id', [
    'name' => 'Renamed Fleet View',
]);
```

## Deleting Records

```php
Samsara::liveShares()->delete('live-share-id');
```

The SDK sends the ID as the collection query parameter required by the operation.

## Filtering

The `query()` method returns a fresh `Builder` rooted at `/live-shares`. Apply filters from the [query builder](../query-builder.html) reference; the live-shares endpoint accepts standard pagination and tag filters.

```php
$shares = Samsara::liveShares()
    ->query()
    ->whereTag('customer-tracking')
    ->get();
```

## Helper Methods

The `LiveShare` entity exposes two helpers:

```php
$share = Samsara::liveShares()->all()->first();

$share->isActive();   // bool
$share->isExpired();  // bool
```

`isExpired()` compares `expiresAtTime` with the current time. A missing expiration is treated as not expired; `isActive()` is the inverse.

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Live share ID. |
| `name` | `?string` | Live share name. |
| `liveSharingUrl` | `?string` | Shareable URL handed to recipients. |
| `type` | `?string` | Link type. |
| `description` | `?string` | Link description. |
| `expiresAtTime` | `?string` | RFC 3339 expiration timestamp. |
| `assetsLocationLinkConfig` | `?array<string, mixed>` | Assets-location configuration. |
| `assetsNearLocationLinkConfig` | `?array<string, mixed>` | Assets-near-location configuration. |
| `assetsOnRouteLinkConfig` | `?array<string, mixed>` | Assets-on-route configuration. |

## Common Use Cases

### Share a Delivery in Progress

Hand a customer a tracking link that auto-expires once delivery is complete:

```php
$share = Samsara::liveShares()->create([
    'name' => "Delivery Tracking — Order #12345",
    'assets' => [
        ['id' => $vehicle->id, 'type' => 'vehicle'],
    ],
    'recipients' => [
        ['email' => $customer->email],
    ],
    'expiresAt' => now()->addHours(4)->toIso8601String(),
]);

$trackingUrl = $share->url;
```

### Revoke Expired Shares

`delete($id)` uses the collection path and can revoke an expired link directly.

## Related Resources

- [Vehicles](vehicles.html) — the assets typically referenced from a live share.
- [Vehicle Locations](vehicle-locations.html) — direct location queries inside the SDK.
- [Webhooks](webhooks.html) — notify on share creation or expiration.

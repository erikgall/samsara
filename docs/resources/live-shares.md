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

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$share = Samsara::liveShares()->find('live-share-id');

$shares = Samsara::liveShares()->all();

$active = Samsara::liveShares()
    ->query()
    ->limit(25)
    ->get();
```

`find()` returns `null` when the share does not exist; `all()` returns an `EntityCollection<int, LiveShare>`.

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

`delete()` is the canonical way to revoke a live share early.

## Filtering

The `query()` method returns a fresh `Builder` rooted at `/live-shares`. Apply filters from the [query builder](../query-builder.md) reference; the live-shares endpoint accepts standard pagination and tag filters.

```php
$shares = Samsara::liveShares()
    ->query()
    ->whereTag('customer-tracking')
    ->get();
```

## Helper Methods

The `LiveShare` entity exposes two helpers:

```php
$share = Samsara::liveShares()->find('live-share-id');

$share->isActive();   // bool
$share->isExpired();  // bool
```

Both check `$share->status` against the literal strings `'active'` and `'expired'`.

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Live share ID. |
| `name` | `?string` | Live share name. |
| `url` | `?string` | Shareable URL handed to recipients. |
| `status` | `?string` | `active` or `expired`. |
| `expiresAt` | `?string` | RFC 3339 expiration timestamp. |
| `createdAtTime` | `?string` | RFC 3339 creation timestamp. |
| `assets` | `?array<int, array{id?: string, type?: string}>` | Assets exposed by the share. |
| `recipients` | `?array<int, array{email?: string}>` | Email recipients. |

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

### Sweep Expired Shares

Remove revoked shares to keep the list short and recipients out of stale data:

```php
Samsara::liveShares()
    ->all()
    ->each(function ($share) {
        if ($share->isExpired()) {
            Samsara::liveShares()->delete($share->id);
        }
    });
```

## Related Resources

- [Vehicles](vehicles.md) — the assets typically referenced from a live share.
- [Vehicle Locations](vehicle-locations.md) — direct location queries inside the SDK.
- [Webhooks](webhooks.md) — notify on share creation or expiration.

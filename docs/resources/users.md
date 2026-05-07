---
title: Users
nav_order: 13
description: Manage dashboard users and their access to your Samsara organization.
permalink: /resources/users
---

# Users

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Auth Types](#auth-types)
- [Helper Methods](#helper-methods)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Users are the people who sign in to the Samsara dashboard — admins, fleet managers, and other back-office accounts. They are distinct from drivers, who are the in-vehicle people that operate gateways and the driver app. If you are managing the dashboard roster, use this resource. For driver records, see [Drivers](drivers.md).

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$users = Samsara::users()->all();

$user = Samsara::users()->find('user-id');
```

## Creating Records

```php
$user = Samsara::users()->create([
    'name' => 'John Smith',
    'email' => 'john.smith@company.com',
    'authType' => 'admin',
    'roles' => [
        ['id' => 'role-id'],
    ],
]);
```

## Updating Records

```php
$user = Samsara::users()->update('user-id', [
    'name' => 'John D. Smith',
]);
```

## Deleting Records

```php
Samsara::users()->delete('user-id');
```

## Filtering

See [Query Builder](../query-builder.md) for the full filter list.

```php
$users = Samsara::users()
    ->query()
    ->whereTag('management-team')
    ->get();

$users = Samsara::users()
    ->query()
    ->limit(25)
    ->get();
```

## Auth Types

Common values you will see on `authType`:

- `admin` — full administrative access.
- `driver` — driver-level access (rare on dashboard users).
- `standard` — standard user access.
- `limited` — read-only or restricted access.

> **Note:** This list is illustrative. The SDK does not ship an `AuthType` enum; consult the Samsara API documentation for the authoritative auth-type catalog used by your organization.

## Helper Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `getDisplayName()` | `string` | The user's `name`, falling back to their email when name is null. |
| `isAdmin()` | `bool` | True when `authType === 'admin'`. |
| `isDriver()` | `bool` | True when `authType === 'driver'`. |

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `string` | User id. |
| `name` | `?string` | User's display name. |
| `email` | `?string` | User's email address. |
| `authType` | `?string` | Authentication type — see above. |
| `roles` | `?array` | Roles assigned to the user (each item exposes `id` and optional `name`). |
| `tagIds` | `?array<int, string>` | Tag ids associated with the user. |
| `createdAtTime` | `?string` | RFC 3339 creation timestamp. |
| `updatedAtTime` | `?string` | RFC 3339 last-update timestamp. |

## Related Resources

- [Drivers](drivers.md) — the in-vehicle people, distinct from dashboard users.
- [Tags](tags.md) — tag the roster for filtering.
- [Contacts](contacts.md) — non-user contacts associated with your organization.

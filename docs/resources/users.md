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

Users are the people who sign in to the Samsara dashboard — admins, fleet managers, and other back-office accounts. They are distinct from drivers, who are the in-vehicle people that operate gateways and the driver app. If you are managing the dashboard roster, use this resource. For driver records, see [Drivers](drivers.html).

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
    'authType' => 'default',
    'roles' => [
        ['roleId' => 'role-id'],
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

The current list-users operation declares only `limit` and `after`. It does not accept tag filters.

```php
$users = Samsara::users()
    ->query()
    ->limit(25)
    ->get();
```

## Auth Types

The current `authType` values are `default` and `saml`. They describe authentication, not authorization or whether a person is a driver. Inspect role assignments for authorization decisions.

## Helper Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `getDisplayName()` | `string` | The user's `name`, falling back to their email when name is null. |
| `getRoleIds()` | `array<int, string>` | Nested `roles[*].role.id` values in response order; malformed assignments are skipped. |

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | `string` | User id. |
| `name` | `?string` | User's display name. |
| `email` | `?string` | User's email address. |
| `authType` | `?string` | Authentication type — see above. |
| `roles` | `?array<int, array{expireAt?: string, role?: array{id?: string, name?: string}, tag?: array<string, mixed>}>` | Organizational or tag-scoped role assignments. |

## Related Resources

- [Drivers](drivers.html) — the in-vehicle people, distinct from dashboard users.
- [Tags](tags.html) — tag the roster for filtering.
- [Contacts](contacts.html) — non-user contacts associated with your organization.

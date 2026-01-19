---
title: Users
layout: default
parent: Resources
nav_order: 13
description: "Manage users and their access to your Samsara organization"
permalink: /resources/users
---

# Users Resource

Manage users and their access to your Samsara organization.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all users
$users = Samsara::users()->all();

// Find a user
$user = Samsara::users()->find('user-id');

// Create a user
$user = Samsara::users()->create([
    'name' => 'John Smith',
    'email' => 'john.smith@company.com',
    'authType' => 'admin',
    'roles' => [
        ['id' => 'role-123'],
    ],
]);

// Update a user
$user = Samsara::users()->update('user-id', [
    'name' => 'John D. Smith',
]);

// Delete a user
Samsara::users()->delete('user-id');
```

## Query Builder

```php
// Filter by tag
$users = Samsara::users()
    ->query()
    ->whereTag('management-team')
    ->get();

// Limit results
$users = Samsara::users()
    ->query()
    ->limit(25)
    ->get();
```

## User Entity

The `User` entity provides helper methods:

```php
$user = Samsara::users()->find('user-id');

// Get display name (falls back to email)
$user->getDisplayName();  // string

// Check user type
$user->isAdmin();   // bool
$user->isDriver();  // bool

// Basic properties
$user->id;            // string
$user->name;          // ?string
$user->email;         // ?string
$user->authType;      // ?string
$user->roles;         // ?array
$user->tagIds;        // ?array
$user->createdAtTime; // ?string
$user->updatedAtTime; // ?string
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | User ID |
| `name` | string | User name |
| `email` | string | User email address |
| `authType` | string | Authentication type ('admin', 'driver', etc.) |
| `roles` | array | User roles with id and name |
| `tagIds` | array | Associated tag IDs |
| `createdAtTime` | string | Creation timestamp (RFC 3339) |
| `updatedAtTime` | string | Last update timestamp (RFC 3339) |

## Auth Types

Common auth types include:

- `admin` - Full administrative access
- `driver` - Driver-level access
- `standard` - Standard user access
- `limited` - Limited access user

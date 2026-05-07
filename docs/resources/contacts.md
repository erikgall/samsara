---
title: Contacts
nav_order: 15
description: Manage contacts associated with addresses and dispatch operations.
permalink: /resources/contacts
---

# Contacts

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Helper Methods](#helper-methods)
- [Using Contacts with Addresses](#using-contacts-with-addresses)
- [Properties](#properties)
- [Related Resources](#related-resources)

## Introduction

Contacts are the people you attach to addresses, dispatch jobs, and routes — typically the recipient or site lead at a customer location. Reach for this resource when you need to maintain a directory of those points of contact and link them to addresses you create.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$contacts = Samsara::contacts()->all();

$contact = Samsara::contacts()->find('contact-id');
```

## Creating Records

```php
$contact = Samsara::contacts()->create([
    'firstName' => 'Jane',
    'lastName'  => 'Doe',
    'email'     => 'jane.doe@customer.com',
    'phone'     => '+1-555-123-4567',
]);
```

## Updating Records

```php
$contact = Samsara::contacts()->update('contact-id', [
    'phone' => '+1-555-987-6543',
]);
```

## Deleting Records

```php
Samsara::contacts()->delete('contact-id');
```

## Filtering

Contacts accept the standard query builder. See [the query builder reference](../query-builder.md) for the full method list.

```php
$contacts = Samsara::contacts()
    ->query()
    ->limit(100)
    ->get();
```

## Helper Methods

The `Contact` entity exposes one helper for assembling the contact's display name:

```php
$contact = Samsara::contacts()->find('contact-id');

$contact->getFullName(); // "Jane Doe", or "Unknown" if both name fields are empty
```

## Using Contacts with Addresses

Contacts are most useful when attached to an address. Pass an array of contact IDs when you create or update an address, then read them back through the address entity.

```php
$address = Samsara::addresses()->create([
    'name'             => 'Customer Warehouse',
    'formattedAddress' => '123 Industrial Blvd, City, ST 12345',
    'contactIds'       => ['contact-123', 'contact-456'],
]);

$address = Samsara::addresses()->find('address-id');

foreach ($address->getContactIds() as $contactId) {
    $contact = Samsara::contacts()->find($contactId);
    echo "{$contact->getFullName()}: {$contact->phone}\n";
}
```

## Properties

The `Contact` entity (`Samsara\Data\Contact\Contact`) exposes the following typed properties.

| Property | Type | Description |
|----------|------|-------------|
| `id` | `?string` | Contact ID. |
| `firstName` | `?string` | First name. |
| `lastName` | `?string` | Last name. |
| `email` | `?string` | Email address. |
| `phone` | `?string` | Phone number. |

## Related Resources

- [Addresses](addresses.md) — attach contacts to a geofenced location.
- [Routes](routes.md) — routes use contacts on their stops.
- [Query Builder](../query-builder.md) — for filtering, pagination, and lazy iteration.
- [Testing](../testing.md) — fake `Samsara::contacts()` calls in your tests.

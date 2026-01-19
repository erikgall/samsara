---
title: Contacts
layout: default
parent: Resources
nav_order: 15
description: "Manage contacts for addresses and dispatch operations"
permalink: /resources/contacts
---

# Contacts Resource

Manage contacts for addresses and dispatch operations.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all contacts
$contacts = Samsara::contacts()->all();

// Find a contact
$contact = Samsara::contacts()->find('contact-id');

// Create a contact
$contact = Samsara::contacts()->create([
    'firstName' => 'Jane',
    'lastName' => 'Doe',
    'email' => 'jane.doe@customer.com',
    'phone' => '+1-555-123-4567',
]);

// Update a contact
$contact = Samsara::contacts()->update('contact-id', [
    'phone' => '+1-555-987-6543',
]);

// Delete a contact
Samsara::contacts()->delete('contact-id');
```

## Query Builder

```php
// Get all contacts with query builder
$contacts = Samsara::contacts()
    ->query()
    ->get();

// Limit results
$contacts = Samsara::contacts()
    ->query()
    ->limit(100)
    ->get();
```

## Contact Entity

The `Contact` entity provides helper methods:

```php
$contact = Samsara::contacts()->find('contact-id');

// Get full name
$contact->getFullName();  // "Jane Doe"

// Basic properties
$contact->id;        // string
$contact->firstName; // ?string
$contact->lastName;  // ?string
$contact->email;     // ?string
$contact->phone;     // ?string
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Contact ID |
| `firstName` | string | First name |
| `lastName` | string | Last name |
| `email` | string | Email address |
| `phone` | string | Phone number |

## Using Contacts with Addresses

Contacts can be associated with addresses:

```php
// Create an address with a contact
$address = Samsara::addresses()->create([
    'name' => 'Customer Warehouse',
    'formattedAddress' => '123 Industrial Blvd, City, ST 12345',
    'contactIds' => ['contact-123', 'contact-456'],
]);

// Get contact IDs from an address
$address = Samsara::addresses()->find('address-id');
$contactIds = $address->getContactIds();

// Fetch the full contact details
foreach ($contactIds as $contactId) {
    $contact = Samsara::contacts()->find($contactId);
    echo "{$contact->getFullName()}: {$contact->phone}";
}
```

---
title: Work Orders
nav_order: 26
description: Manage maintenance work orders, service tasks, and invoice scans.
permalink: /resources/work-orders
---

# Work Orders

- [Introduction](#introduction)
- [Retrieving Records](#retrieving-records)
- [Creating Records](#creating-records)
- [Updating Records](#updating-records)
- [Deleting Records](#deleting-records)
- [Filtering](#filtering)
- [Streaming](#streaming)
- [Service Tasks](#service-tasks)
- [Invoice Scans](#invoice-scans)
- [Related Resources](#related-resources)

## Introduction

Work orders track the maintenance lifecycle for a vehicle or asset — what needs servicing, who is doing the work, and what tasks (oil change, tire rotation, brake inspection) the order covers. Use `query()` to filter the collection, `stream()` for back-fill across long windows, and `serviceTasks()` for the catalog of available tasks.

The resource owns generic `all` and `create`. Generic `find`, `update`, and `delete` are rejected because the current API uses collection-style identifiers that those generic signatures do not model. `query()`, `stream()`, `serviceTasks()`, and `uploadInvoiceScan()` implement the current read and upload paths.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$workOrders = Samsara::workOrders()->all();

$workOrder = Samsara::workOrders()->query()->where('ids', ['work-order-id'])->first();
```

## Creating Records

```php
$workOrder = Samsara::workOrders()->create([
    'vehicleId' => 'vehicle-id',
    'description' => 'Oil change and tire rotation',
    'serviceTasks' => [
        ['id' => 'task-oil-change'],
        ['id' => 'task-tire-rotation'],
    ],
]);
```

## Updating Records

The SDK does not yet expose the collection-style update operation. Generic `update()` throws locally.

## Deleting Records

The SDK does not yet expose the collection-style delete operation. Generic `delete()` throws locally.

## Filtering

Use `query()` to apply filters to the standard endpoint. See [Query Builder](../query-builder.html) for the full filter list.

```php
$workOrders = Samsara::workOrders()
    ->query()
    ->whereTag('fleet-maintenance')
    ->get();
```

## Streaming

The `stream()` builder targets the streaming endpoint and accepts a time window. Use it for back-fill across long ranges.

```php
use Carbon\Carbon;

$workOrders = Samsara::workOrders()
    ->stream()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();

// Iterate without buffering every page
Samsara::workOrders()
    ->stream()
    ->between(Carbon::now()->subYear(), Carbon::now())
    ->lazy(500)
    ->each(function ($workOrder) {
        // Process each work order
    });
```

## Service Tasks

`serviceTasks()` returns a no-implicit-limit builder of `Samsara\Data\Maintenance\ServiceTask` objects. Arrays passed as `ids` use repeated query keys.

```php
$serviceTasks = Samsara::workOrders()->serviceTasks()->get();

foreach ($serviceTasks as $task) {
    echo "{$task->id}: {$task->name}\n";
}
```

The terminal `get()` call returns `EntityCollection<int, ServiceTask>`.

The package's `MaintenanceStatus` enum contains four lowercase values, while the current upstream Work Orders schema defines eleven title-cased statuses. Treat status handling as a pending package-contract reconciliation.

## Invoice Scans

`uploadInvoiceScan()` posts a metadata payload to `/maintenance/invoice-scans` and returns the API's raw `data` envelope as an array — useful for attaching invoices to a work order.

```php
$result = Samsara::workOrders()->uploadInvoiceScan([
    'workOrderId' => 'work-order-id',
    'fileName' => 'invoice.pdf',
    'contentType' => 'application/pdf',
]);
```

## Related Resources

- [Maintenance](maintenance.html) — DVIRs and defects feeding into work orders.
- [Vehicles](vehicles.html) — vehicles a work order targets.
- [Issues](issues.html) — active vehicle and equipment issues.

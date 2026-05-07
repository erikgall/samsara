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

Work orders track the maintenance lifecycle for a vehicle or asset — what needs servicing, who is doing the work, and what tasks (oil change, tire rotation, brake inspection) the order covers. Use the standard CRUD methods for managing individual records, `query()` to filter on tags, `stream()` for back-fill across long windows, and `serviceTasks()` for the catalog of available tasks.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$workOrders = Samsara::workOrders()->all();

$workOrder = Samsara::workOrders()->find('work-order-id');
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

```php
$workOrder = Samsara::workOrders()->update('work-order-id', [
    'status' => 'completed',
]);
```

## Deleting Records

```php
Samsara::workOrders()->delete('work-order-id');
```

## Filtering

Use `query()` to apply filters to the standard endpoint. See [Query Builder](../query-builder.md) for the full filter list.

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

`serviceTasks()` returns the catalog of service-task definitions that work orders can reference. Each task is a generic `Entity` carrying at minimum an `id` and `name`.

```php
$serviceTasks = Samsara::workOrders()->serviceTasks();

foreach ($serviceTasks as $task) {
    echo "{$task->id}: {$task->name}\n";
}
```

> **Note:** `serviceTasks()` returns an `EntityCollection<int, Entity>`, not an `EntityCollection<int, WorkOrder>`. The shape of each item follows the `/maintenance/service-tasks` API response.

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

- [Maintenance](maintenance.md) — DVIRs and defects feeding into work orders.
- [Vehicles](vehicles.md) — vehicles a work order targets.
- [Issues](issues.md) — active vehicle and equipment issues.

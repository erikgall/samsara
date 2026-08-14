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

The resource owns generic `all` and `create`. Generic `find`, `update`, and `delete` remain rejected because the current API uses collection-style identifiers that those generic signatures do not model. Use `updateWorkOrder()` and `deleteWorkOrder()` for the contract-backed collection operations.

## Retrieving Records

```php
use Samsara\Facades\Samsara;

$workOrders = Samsara::workOrders()->all();

$workOrder = Samsara::workOrders()
    ->query()
    ->where('ids', ['work-order-id'])
    ->get()
    ->first();
```

## Creating Records

```php
$workOrder = Samsara::workOrders()->create([
    'assetId' => 'vehicle-id',
    'description' => 'Oil change and tire rotation',
    'serviceTaskInstances' => [
        ['serviceTaskId' => 'task-oil-change', 'status' => 'Open'],
        ['serviceTaskId' => 'task-tire-rotation', 'status' => 'Open'],
    ],
]);
```

## Updating Records

```php
$workOrder = Samsara::workOrders()->updateWorkOrder([
    'id' => 'work-order-id',
    'status' => 'Completed',
]);
```

The required work-order ID belongs in the PATCH body. Generic `update()` still throws locally rather than emitting a fabricated item path.

## Deleting Records

```php
Samsara::workOrders()->deleteWorkOrder('work-order-id');
```

`deleteWorkOrder()` sends the ID as the required collection query parameter and returns `true` after a successful 204 response.

## Filtering

Use `query()` with the standard endpoint's declared `ids`, `after`, and `includeExternalIds` parameters. See [Query Builder](../query-builder.html) for the fluent method mapping.

```php
$workOrders = Samsara::workOrders()
    ->query()
    ->where('ids', ['work-order-1', 'work-order-2'])
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
    ->lazy()
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

The package's `MaintenanceStatus` enum is legacy vocabulary and is not the current Work Orders wire contract. Work-order status fields use the exact title-cased values documented by the current API, including `Unknown`, `Open`, `In Progress`, `On Hold`, and `Completed`; do not send the lowercase enum values for these fields.

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

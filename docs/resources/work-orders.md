# Work Orders Resource

Manage maintenance work orders and service tasks.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all work orders
$workOrders = Samsara::workOrders()->all();

// Find a work order
$workOrder = Samsara::workOrders()->find('work-order-id');

// Create a work order
$workOrder = Samsara::workOrders()->create([
    'vehicleId' => 'vehicle-123',
    'description' => 'Oil change and tire rotation',
    'serviceTasks' => [
        ['id' => 'task-oil-change'],
        ['id' => 'task-tire-rotation'],
    ],
]);

// Update a work order
$workOrder = Samsara::workOrders()->update('work-order-id', [
    'status' => 'completed',
]);

// Delete a work order
Samsara::workOrders()->delete('work-order-id');
```

## Query Builder

```php
// Get work orders with query builder
$workOrders = Samsara::workOrders()
    ->query()
    ->get();

// Filter by tag
$workOrders = Samsara::workOrders()
    ->query()
    ->whereTag('fleet-maintenance')
    ->get();
```

## Work Orders Stream

```php
use Carbon\Carbon;

// Get work orders stream
$workOrders = Samsara::workOrders()
    ->stream()
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();
```

## Service Tasks

```php
// Get all service tasks
$serviceTasks = Samsara::workOrders()->serviceTasks();
```

## Invoice Scans

```php
// Upload an invoice scan
$result = Samsara::workOrders()->uploadInvoiceScan([
    'workOrderId' => 'work-order-123',
    'fileName' => 'invoice.pdf',
    'contentType' => 'application/pdf',
]);
```

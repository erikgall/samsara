# Gateways Resource

Manage gateway devices attached to vehicles.

## Basic Usage

```php
use Samsara\Facades\Samsara;

// Get all gateways
$gateways = Samsara::gateways()->all();

// Find a gateway
$gateway = Samsara::gateways()->find('gateway-id');
```

## Gateway Entity

```php
$gateway = Samsara::gateways()->find('gateway-id');

$gateway->serial; // ?string - Gateway serial number
$gateway->model;  // ?string - Gateway model
```

## Available Properties

| Property | Type | Description |
|----------|------|-------------|
| `serial` | string | Gateway serial number |
| `model` | string | Gateway model |

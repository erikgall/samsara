# Settings Resource

Access and manage organization settings.

## Compliance Settings

```php
use Samsara\Facades\Samsara;

// Get compliance settings
$compliance = Samsara::settings()->compliance();

// Update compliance settings
$updated = Samsara::settings()->updateCompliance([
    'hosRuleset' => 'usa_70_8',
    'restBreakRequired' => true,
]);
```

## Driver App Settings

```php
// Get driver app settings
$driverApp = Samsara::settings()->driverApp();

// Update driver app settings
$updated = Samsara::settings()->updateDriverApp([
    'allowManualDutyStatus' => true,
    'requireSignatureOnDelivery' => true,
]);
```

## Safety Settings

```php
// Get safety settings
$safety = Samsara::settings()->safety();
```

## Available Settings

### Compliance Settings

Settings related to HOS (Hours of Service) and regulatory compliance.

### Driver App Settings

Settings that control driver app behavior and requirements.

### Safety Settings

Settings related to safety monitoring and alerts.

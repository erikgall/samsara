# Samsara ELD Laravel SDK - Resource Guidelines

## Overview

Resources are accessed through the Samsara facade or client instance. Each resource provides CRUD operations plus specialized methods for domain-specific functionality.

@verbatim
<code-snippet name="Resource access pattern" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Access any resource via facade
$drivers = Samsara::drivers();
$vehicles = Samsara::vehicles();
$hoursOfService = Samsara::hoursOfService();
$vehicleStats = Samsara::vehicleStats();
</code-snippet>
@endverbatim

---

## Fleet Resources

### Drivers Resource

The drivers resource provides driver management with activation controls and authentication features.

#### Activate/Deactivate Drivers

@verbatim
<code-snippet name="Activate and deactivate drivers" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Activate a driver
$driver = Samsara::drivers()->activate('driver-123');

// Deactivate a driver
$driver = Samsara::drivers()->deactivate('driver-123');
</code-snippet>
@endverbatim

#### Query Active/Deactivated Drivers

@verbatim
<code-snippet name="Query drivers by activation status" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all active drivers
$activeDrivers = Samsara::drivers()->active()->get();

// Get all deactivated drivers
$deactivatedDrivers = Samsara::drivers()->deactivated()->get();

// Chain with other filters
$activeInFleet = Samsara::drivers()
    ->active()
    ->whereTag('fleet-west')
    ->get();
</code-snippet>
@endverbatim

#### Find by External ID

@verbatim
<code-snippet name="Find driver by external system ID" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Find driver by external ID from your system
$driver = Samsara::drivers()->findByExternalId('mySystem', 'EMP-12345');

if ($driver === null) {
    // Driver not found with this external ID
}
</code-snippet>
@endverbatim

#### Driver Authentication

@verbatim
<code-snippet name="Create driver authentication tokens" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Create auth token for driver app login
$token = Samsara::drivers()->createAuthToken('driver-123');

// Remote sign out a driver
Samsara::drivers()->remoteSignOut('driver-123');
</code-snippet>
@endverbatim

#### QR Codes for Driver App

@verbatim
<code-snippet name="Manage driver QR codes" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all QR codes
$qrCodes = Samsara::drivers()->getQrCodes();

// Create a new QR code
$qrCode = Samsara::drivers()->createQrCode([
    'name' => 'Warehouse Login',
    'vehicleId' => 'vehicle-123',
]);

// Delete a QR code
Samsara::drivers()->deleteQrCode('qr-code-id');
</code-snippet>
@endverbatim

### Vehicles Resource

@verbatim
<code-snippet name="Vehicle management operations" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all vehicles
$vehicles = Samsara::vehicles()->all();

// Find vehicle by ID
$vehicle = Samsara::vehicles()->find('vehicle-123');

// Query with filters
$trucks = Samsara::vehicles()
    ->query()
    ->whereTag('trucks')
    ->get();

// Create a vehicle
$vehicle = Samsara::vehicles()->create([
    'name' => 'Truck 101',
    'vin' => '1HGBH41JXMN109186',
    'licensePlate' => 'ABC-1234',
]);
</code-snippet>
@endverbatim

### Trailers Resource

@verbatim
<code-snippet name="Trailer management operations" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all trailers
$trailers = Samsara::trailers()->all();

// Find trailer
$trailer = Samsara::trailers()->find('trailer-123');

// Create trailer
$trailer = Samsara::trailers()->create([
    'name' => 'Trailer T-500',
    'serialNumber' => 'SN12345678',
]);
</code-snippet>
@endverbatim

### Equipment Resource

@verbatim
<code-snippet name="Equipment tracking operations" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all equipment
$equipment = Samsara::equipment()->all();

// Find equipment
$item = Samsara::equipment()->find('equipment-123');
</code-snippet>
@endverbatim

---

## Telematics Resources

### Vehicle Stats Resource

The vehicle stats resource provides access to vehicle telemetry data including GPS, fuel, engine states, and odometer readings.

#### Current Stats

@verbatim
<code-snippet name="Get current vehicle stats" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get current stats for all vehicles
$stats = Samsara::vehicleStats()->current()->get();

// Get current stats for specific vehicles
$stats = Samsara::vehicleStats()
    ->current()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->get();
</code-snippet>
@endverbatim

#### Historical Stats

@verbatim
<code-snippet name="Get historical vehicle stats" lang="php">
use ErikGall\Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get stats history for a time period
$history = Samsara::vehicleStats()
    ->history()
    ->whereVehicle('vehicle-123')
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->types(['gps', 'fuelPercents'])
    ->get();
</code-snippet>
@endverbatim

#### Stats Feed (Real-time Updates)

@verbatim
<code-snippet name="Get vehicle stats feed" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get stats feed (for polling new data)
$feed = Samsara::vehicleStats()
    ->feed()
    ->types(['gps', 'engineStates'])
    ->get();
</code-snippet>
@endverbatim

#### Specific Stat Types

@verbatim
<code-snippet name="Query specific telemetry types" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// GPS data only
$gps = Samsara::vehicleStats()->gps()->get();

// Fuel percentages
$fuel = Samsara::vehicleStats()->fuelPercents()->get();

// Engine states (on/off/idle)
$engine = Samsara::vehicleStats()->engineStates()->get();

// Odometer readings (OBD and GPS)
$odometer = Samsara::vehicleStats()->odometer()->get();
</code-snippet>
@endverbatim

### Vehicle Locations Resource

@verbatim
<code-snippet name="Get real-time vehicle locations" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get current locations for all vehicles
$locations = Samsara::vehicleLocations()->all();

// Query with filters
$locations = Samsara::vehicleLocations()
    ->query()
    ->whereTag('active-fleet')
    ->get();
</code-snippet>
@endverbatim

### Trips Resource

@verbatim
<code-snippet name="Query trip data" lang="php">
use ErikGall\Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get trips for a driver
$trips = Samsara::trips()
    ->query()
    ->whereDriver('driver-123')
    ->between(Carbon::now()->subWeek(), Carbon::now())
    ->get();

// Get trips for vehicles
$trips = Samsara::trips()
    ->query()
    ->whereVehicle(['vehicle-1', 'vehicle-2'])
    ->between(Carbon::today()->startOfDay(), Carbon::today()->endOfDay())
    ->get();
</code-snippet>
@endverbatim

---

## Safety Resources

### Hours of Service Resource

The HOS resource provides access to driver duty status logs, clocks, violations, and daily logs for ELD compliance.

#### HOS Logs

@verbatim
<code-snippet name="Query HOS duty status logs" lang="php">
use ErikGall\Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get HOS logs for drivers
$logs = Samsara::hoursOfService()
    ->logs()
    ->whereDriver(['driver-1', 'driver-2'])
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();

// Stream through large log datasets
$logs = Samsara::hoursOfService()
    ->logs()
    ->between(Carbon::now()->subMonth(), Carbon::now())
    ->lazy(500)
    ->each(function ($log) {
        processLog($log);
    });
</code-snippet>
@endverbatim

#### HOS Clocks

@verbatim
<code-snippet name="Get driver HOS clocks" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get current HOS clocks for drivers
$clocks = Samsara::hoursOfService()
    ->clocks()
    ->whereDriver('driver-123')
    ->get();

// Get clocks for all drivers in a tag
$clocks = Samsara::hoursOfService()
    ->clocks()
    ->whereTag('long-haul')
    ->get();
</code-snippet>
@endverbatim

#### HOS Violations

@verbatim
<code-snippet name="Query HOS violations" lang="php">
use ErikGall\Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get violations for a driver
$violations = Samsara::hoursOfService()
    ->violations()
    ->whereDriver('driver-123')
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();

// Get all violations in fleet
$allViolations = Samsara::hoursOfService()
    ->violations()
    ->whereTag('all-drivers')
    ->between(Carbon::now()->startOfMonth(), Carbon::now())
    ->get();
</code-snippet>
@endverbatim

#### HOS Daily Logs

@verbatim
<code-snippet name="Query HOS daily logs" lang="php">
use ErikGall\Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get daily logs for compliance reporting
$dailyLogs = Samsara::hoursOfService()
    ->dailyLogs()
    ->whereDriver('driver-123')
    ->between(Carbon::now()->subDays(14), Carbon::now())
    ->get();
</code-snippet>
@endverbatim

### Safety Events Resource

@verbatim
<code-snippet name="Query safety events" lang="php">
use ErikGall\Samsara\Facades\Samsara;
use Carbon\Carbon;

// Get safety events (harsh braking, speeding, etc.)
$events = Samsara::safetyEvents()
    ->query()
    ->whereVehicle('vehicle-123')
    ->between(Carbon::now()->subWeek(), Carbon::now())
    ->get();
</code-snippet>
@endverbatim

### Maintenance Resource

@verbatim
<code-snippet name="Access maintenance and DVIR data" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get maintenance records
$maintenance = Samsara::maintenance()->all();

// Query DVIRs
$dvirs = Samsara::maintenance()
    ->query()
    ->whereVehicle('vehicle-123')
    ->get();
</code-snippet>
@endverbatim

---

## Dispatch Resources

### Routes Resource

@verbatim
<code-snippet name="Route management operations" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all routes
$routes = Samsara::routes()->all();

// Create a route
$route = Samsara::routes()->create([
    'name' => 'Delivery Route A',
    'driverId' => 'driver-123',
    'scheduledStartTime' => '2024-01-15T08:00:00Z',
    'stops' => [
        ['addressId' => 'address-1', 'scheduledArrivalTime' => '2024-01-15T09:00:00Z'],
        ['addressId' => 'address-2', 'scheduledArrivalTime' => '2024-01-15T10:30:00Z'],
    ],
]);

// Update route
$route = Samsara::routes()->update('route-123', [
    'name' => 'Updated Route Name',
]);

// Delete route
Samsara::routes()->delete('route-123');
</code-snippet>
@endverbatim

### Addresses Resource

@verbatim
<code-snippet name="Address/geofence management" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all addresses
$addresses = Samsara::addresses()->all();

// Create an address with geofence
$address = Samsara::addresses()->create([
    'name' => 'Distribution Center',
    'formattedAddress' => '123 Warehouse Blvd, City, ST 12345',
    'geofence' => [
        'circle' => [
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'radiusMeters' => 200,
        ],
    ],
]);

// Update address
$address = Samsara::addresses()->update('address-123', [
    'name' => 'Main Distribution Center',
]);
</code-snippet>
@endverbatim

---

## Organization Resources

### Tags Resource

@verbatim
<code-snippet name="Tag management for organizing fleet" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all tags
$tags = Samsara::tags()->all();

// Create a tag
$tag = Samsara::tags()->create([
    'name' => 'West Coast Fleet',
    'parentTagId' => 'parent-tag-123', // Optional
]);

// Update tag
$tag = Samsara::tags()->update('tag-123', [
    'name' => 'Pacific Northwest Fleet',
]);

// Delete tag
Samsara::tags()->delete('tag-123');
</code-snippet>
@endverbatim

### Users Resource

@verbatim
<code-snippet name="User management operations" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all users
$users = Samsara::users()->all();

// Find user
$user = Samsara::users()->find('user-123');

// Create user
$user = Samsara::users()->create([
    'name' => 'Jane Smith',
    'email' => 'jane.smith@example.com',
    'roles' => ['FleetManager'],
]);
</code-snippet>
@endverbatim

### Contacts Resource

@verbatim
<code-snippet name="Contact management" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all contacts
$contacts = Samsara::contacts()->all();

// Create contact
$contact = Samsara::contacts()->create([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '555-0123',
]);
</code-snippet>
@endverbatim

---

## Integration Resources

### Webhooks Resource

@verbatim
<code-snippet name="Webhook management" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all webhooks
$webhooks = Samsara::webhooks()->all();

// Create webhook
$webhook = Samsara::webhooks()->create([
    'name' => 'Fleet Events Webhook',
    'url' => 'https://example.com/webhooks/samsara',
    'eventTypes' => [
        'VehicleCreated',
        'VehicleUpdated',
        'DriverCreated',
        'GeofenceEntry',
        'GeofenceExit',
    ],
]);

// Update webhook
$webhook = Samsara::webhooks()->update('webhook-123', [
    'url' => 'https://new-endpoint.com/webhooks',
]);

// Delete webhook
Samsara::webhooks()->delete('webhook-123');
</code-snippet>
@endverbatim

### Gateways Resource

@verbatim
<code-snippet name="Gateway management" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all gateways
$gateways = Samsara::gateways()->all();

// Find gateway
$gateway = Samsara::gateways()->find('gateway-123');
</code-snippet>
@endverbatim

### Live Sharing Links Resource

@verbatim
<code-snippet name="Live sharing link management" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get all live shares
$liveShares = Samsara::liveShares()->all();

// Create live share link
$liveShare = Samsara::liveShares()->create([
    'name' => 'Customer Tracking Link',
    'driverId' => 'driver-123',
    'expiresAt' => '2024-12-31T23:59:59Z',
]);
</code-snippet>
@endverbatim

---

## Additional Resources

### Alerts Resource

@verbatim
<code-snippet name="Alert configuration" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$alerts = Samsara::alerts()->all();
</code-snippet>
@endverbatim

### IFTA Resource

@verbatim
<code-snippet name="IFTA reporting data" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$ifta = Samsara::ifta()->all();
</code-snippet>
@endverbatim

### Idling Resource

@verbatim
<code-snippet name="Idling reports" lang="php">
use ErikGall\Samsara\Facades\Samsara;
use Carbon\Carbon;

$idling = Samsara::idling()
    ->query()
    ->whereVehicle('vehicle-123')
    ->between(Carbon::now()->subWeek(), Carbon::now())
    ->get();
</code-snippet>
@endverbatim

### Fuel and Energy Resource

@verbatim
<code-snippet name="Fuel and energy data" lang="php">
use ErikGall\Samsara\Facades\Samsara;

$fuelData = Samsara::fuelAndEnergy()->all();
</code-snippet>
@endverbatim

### Work Orders Resource

@verbatim
<code-snippet name="Work order management" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Get work orders
$workOrders = Samsara::workOrders()->all();

// Create work order
$workOrder = Samsara::workOrders()->create([
    'vehicleId' => 'vehicle-123',
    'type' => 'maintenance',
    'description' => 'Oil change due',
]);
</code-snippet>
@endverbatim

### Assignment Resources

@verbatim
<code-snippet name="Manage driver and equipment assignments" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Driver-vehicle assignments
$assignments = Samsara::driverVehicleAssignments()->all();

// Driver-trailer assignments
$trailerAssignments = Samsara::driverTrailerAssignments()->all();

// Trailer assignments
$trailers = Samsara::trailerAssignments()->all();

// Carrier proposed assignments
$proposed = Samsara::carrierProposedAssignments()->all();
</code-snippet>
@endverbatim

---

## Beta/Preview/Legacy Resources

### Beta Resource

@verbatim
<code-snippet name="Access beta API features" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Access beta endpoints (may change without notice)
$beta = Samsara::beta();
</code-snippet>
@endverbatim

### Preview Resource

@verbatim
<code-snippet name="Access preview API features" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Access preview endpoints (early testing)
$preview = Samsara::preview();
</code-snippet>
@endverbatim

### Legacy Resource

@verbatim
<code-snippet name="Access legacy v1 endpoints" lang="php">
use ErikGall\Samsara\Facades\Samsara;

// Access deprecated v1 endpoints
$legacy = Samsara::legacy();
</code-snippet>
@endverbatim

---

## Resource Access Reference

| Method | Resource | Description |
|--------|----------|-------------|
| `drivers()` | DriversResource | Driver management |
| `vehicles()` | VehiclesResource | Vehicle management |
| `trailers()` | TrailersResource | Trailer management |
| `equipment()` | EquipmentResource | Equipment tracking |
| `vehicleStats()` | VehicleStatsResource | Vehicle telemetry |
| `vehicleLocations()` | VehicleLocationsResource | Real-time locations |
| `trips()` | TripsResource | Trip data |
| `hoursOfService()` | HoursOfServiceResource | HOS/ELD compliance |
| `maintenance()` | MaintenanceResource | Maintenance & DVIRs |
| `safetyEvents()` | SafetyEventsResource | Safety events |
| `routes()` | RoutesResource | Route management |
| `addresses()` | AddressesResource | Addresses/geofences |
| `users()` | UsersResource | User management |
| `tags()` | TagsResource | Tag management |
| `contacts()` | ContactsResource | Contact management |
| `webhooks()` | WebhooksResource | Webhook management |
| `gateways()` | GatewaysResource | Gateway management |
| `liveShares()` | LiveSharingLinksResource | Live sharing links |
| `alerts()` | AlertsResource | Alert configuration |
| `ifta()` | IftaResource | IFTA reporting |
| `idling()` | IdlingResource | Idling reports |
| `fuelAndEnergy()` | FuelAndEnergyResource | Fuel/energy data |
| `workOrders()` | WorkOrdersResource | Work orders |

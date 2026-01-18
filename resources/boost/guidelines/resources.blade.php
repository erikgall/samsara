# Samsara Resource Reference

Resource-specific methods beyond standard CRUD operations.

## Drivers Resource

@verbatim
<code-snippet name="Driver-specific methods" lang="php">
use Samsara\Facades\Samsara;

// Activation status
Samsara::drivers()->activate('driver-123');
Samsara::drivers()->deactivate('driver-123');

// Query by activation
$active = Samsara::drivers()->active()->get();
$deactivated = Samsara::drivers()->deactivated()->get();

// Find by external ID
$driver = Samsara::drivers()->findByExternalId('mySystem', 'EMP-12345');

// Authentication
$token = Samsara::drivers()->createAuthToken('driver-123');
Samsara::drivers()->remoteSignOut('driver-123');

// QR codes
$qrCodes = Samsara::drivers()->getQrCodes();
$qrCode = Samsara::drivers()->createQrCode(['name' => 'Warehouse', 'vehicleId' => 'v-123']);
Samsara::drivers()->deleteQrCode('qr-id');
</code-snippet>
@endverbatim

## Vehicle Stats Resource

@verbatim
<code-snippet name="Vehicle telemetry access" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Current snapshot
$stats = Samsara::vehicleStats()->current()->get();

// Historical data
$history = Samsara::vehicleStats()
    ->history()
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->types(['gps', 'fuelPercents'])
    ->get();

// Real-time feed
$feed = Samsara::vehicleStats()->feed()->types(['gps', 'engineStates'])->get();

// Specific stat types
$gps = Samsara::vehicleStats()->gps()->get();
$fuel = Samsara::vehicleStats()->fuelPercents()->get();
$engine = Samsara::vehicleStats()->engineStates()->get();
$odometer = Samsara::vehicleStats()->odometer()->get();
</code-snippet>
@endverbatim

## Hours of Service Resource

@verbatim
<code-snippet name="HOS compliance data" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Duty status logs
$logs = Samsara::hoursOfService()
    ->logs()
    ->whereDriver(['d-1', 'd-2'])
    ->between(Carbon::now()->subDays(7), Carbon::now())
    ->get();

// Current clocks
$clocks = Samsara::hoursOfService()->clocks()->whereTag('long-haul')->get();

// Violations
$violations = Samsara::hoursOfService()
    ->violations()
    ->whereDriver('driver-123')
    ->between(Carbon::now()->subDays(30), Carbon::now())
    ->get();

// Daily logs
$dailyLogs = Samsara::hoursOfService()
    ->dailyLogs()
    ->whereDriver('driver-123')
    ->between(Carbon::now()->subDays(14), Carbon::now())
    ->get();
</code-snippet>
@endverbatim

## Routes & Addresses

@verbatim
<code-snippet name="Dispatch resources" lang="php">
use Samsara\Facades\Samsara;

// Create route with stops
$route = Samsara::routes()->create([
    'name' => 'Delivery Route A',
    'driverId' => 'driver-123',
    'scheduledStartTime' => '2024-01-15T08:00:00Z',
    'stops' => [
        ['addressId' => 'addr-1', 'scheduledArrivalTime' => '2024-01-15T09:00:00Z'],
        ['addressId' => 'addr-2', 'scheduledArrivalTime' => '2024-01-15T10:30:00Z'],
    ],
]);

// Create address with geofence
$address = Samsara::addresses()->create([
    'name' => 'Distribution Center',
    'formattedAddress' => '123 Warehouse Blvd, City, ST 12345',
    'geofence' => [
        'circle' => ['latitude' => 37.7749, 'longitude' => -122.4194, 'radiusMeters' => 200],
    ],
]);
</code-snippet>
@endverbatim

## Webhooks

@verbatim
<code-snippet name="Webhook management" lang="php">
use Samsara\Facades\Samsara;

$webhook = Samsara::webhooks()->create([
    'name' => 'Fleet Events',
    'url' => 'https://example.com/webhooks/samsara',
    'eventTypes' => ['VehicleCreated', 'VehicleUpdated', 'GeofenceEntry', 'GeofenceExit'],
]);
</code-snippet>
@endverbatim

## Tags

@verbatim
<code-snippet name="Tag hierarchy" lang="php">
use Samsara\Facades\Samsara;

// Create parent tag
$parent = Samsara::tags()->create(['name' => 'Regions']);

// Create child tag
$child = Samsara::tags()->create(['name' => 'West Coast', 'parentTagId' => $parent->getId()]);
</code-snippet>
@endverbatim

## Other Resources

@verbatim
<code-snippet name="Additional resource examples" lang="php">
use Samsara\Facades\Samsara;
use Carbon\Carbon;

// Safety events
$events = Samsara::safetyEvents()
    ->query()
    ->whereVehicle('v-123')
    ->between(Carbon::now()->subWeek(), Carbon::now())
    ->get();

// Trips
$trips = Samsara::trips()
    ->query()
    ->whereDriver('driver-123')
    ->between(Carbon::now()->subWeek(), Carbon::now())
    ->get();

// Vehicle locations
$locations = Samsara::vehicleLocations()->all();

// Idling
$idling = Samsara::idling()
    ->query()
    ->whereVehicle('v-123')
    ->between(Carbon::now()->subWeek(), Carbon::now())
    ->get();

// Work orders
$workOrders = Samsara::workOrders()->all();

// Assignments
$driverVehicle = Samsara::driverVehicleAssignments()->all();
$driverTrailer = Samsara::driverTrailerAssignments()->all();
</code-snippet>
@endverbatim

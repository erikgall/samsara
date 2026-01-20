<?php

namespace Samsara\Enums;

use Illuminate\Support\Collection;

/**
 * Webhook event type enum.
 *
 * Note: Event availability may vary. GA events are generally available,
 * while Beta events may change or have limited availability.
 * See: https://developers.samsara.com/docs/event-subscriptions
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum WebhookEvent: string
{
    // Beta Events - Addresses
    case ADDRESS_CREATED = 'AddressCreated';
    case ADDRESS_DELETED = 'AddressDeleted';
    case ADDRESS_UPDATED = 'AddressUpdated';
    // GA (Generally Available) Events
    case ALERT_INCIDENT = 'AlertIncident';

    // Beta Events - Assets
    case ASSET_CREATED = 'AssetCreated';
    case ASSET_DELETED = 'AssetDeleted';
    case ASSET_UPDATED = 'AssetUpdated';

    // Beta Events - Coaching
    case COACHING_SESSION_CREATED = 'CoachingSessionCreated';
    case COACHING_SESSION_UPDATED = 'CoachingSessionUpdated';

    // Beta Events - Documents
    case DOCUMENT_SUBMITTED = 'DocumentSubmitted';

    // Beta Events - Drivers
    case DRIVER_CREATED = 'DriverCreated';
    case DRIVER_DELETED = 'DriverDeleted';
    case DRIVER_UPDATED = 'DriverUpdated';

    // Beta Events - DVIR
    case DVIR_DEFECT_RESOLVED = 'DvirDefectResolved';
    case DVIR_SUBMITTED = 'DvirSubmitted';

    // Beta Events - Engine
    case ENGINE_FAULT_OFF = 'EngineFaultOff';
    case ENGINE_FAULT_ON = 'EngineFaultOn';

    // Beta Events - Equipment
    case EQUIPMENT_CREATED = 'EquipmentCreated';
    case EQUIPMENT_DELETED = 'EquipmentDeleted';
    case EQUIPMENT_UPDATED = 'EquipmentUpdated';

    // Beta Events - Forms
    case FORM_SUBMITTED = 'FormSubmitted';
    case FORM_UPDATED = 'FormUpdated';

    // Beta Events - Gateway
    case GATEWAY_HEARTBEAT = 'GatewayHeartbeat';
    case GATEWAY_UNPLUGGED = 'GatewayUnplugged';

    // Beta Events - Geofence
    case GEOFENCE_ENTRY = 'GeofenceEntry';
    case GEOFENCE_EXIT = 'GeofenceExit';

    // Beta Events - HOS (Hours of Service)
    case HOS_CLOCKS_UPDATED = 'HosClocksUpdated';
    case HOS_LOGS_UPDATED = 'HosLogsUpdated';
    case HOS_VIOLATION = 'HosViolation';

    // Beta Events - Issues
    case ISSUE_CREATED = 'IssueCreated';
    case ISSUE_UPDATED = 'IssueUpdated';

    // Beta Events - Live Share
    case LIVE_SHARE_CREATED = 'LiveShareCreated';
    case LIVE_SHARE_EXPIRED = 'LiveShareExpired';

    // Beta Events - Panic
    case PANIC_BUTTON_PRESSED = 'PanicButtonPressed';

    // Beta Events - Predictive Maintenance
    case PREDICTIVE_MAINTENANCE_ALERT = 'PredictiveMaintenanceAlert';

    // Beta Events - Routes
    case ROUTE_CREATED = 'RouteCreated';
    case ROUTE_DELETED = 'RouteDeleted';
    case ROUTE_STOP_ARRIVAL = 'RouteStopArrival';
    case ROUTE_STOP_DEPARTURE = 'RouteStopDeparture';
    case ROUTE_STOP_EARLY_LATE_ARRIVAL = 'RouteStopEarlyLateArrival';
    case ROUTE_STOP_ETA_UPDATED = 'RouteStopEtaUpdated';
    case ROUTE_STOP_RESEQUENCE = 'RouteStopResequence';
    case ROUTE_UPDATED = 'RouteUpdated';

    // Beta Events - Safety
    case SAFETY_EVENT_CREATED = 'SafetyEventCreated';
    case SEVERE_SPEEDING_ENDED = 'SevereSpeedingEnded';
    case SEVERE_SPEEDING_STARTED = 'SevereSpeedingStarted';

    // Beta Events - Speeding
    case SPEEDING_EVENT_ENDED = 'SpeedingEventEnded';
    case SPEEDING_EVENT_STARTED = 'SpeedingEventStarted';

    // Beta Events - Tags
    case TAG_CREATED = 'TagCreated';
    case TAG_DELETED = 'TagDeleted';
    case TAG_UPDATED = 'TagUpdated';

    // Beta Events - Trailers
    case TRAILER_CREATED = 'TrailerCreated';
    case TRAILER_DELETED = 'TrailerDeleted';
    case TRAILER_UPDATED = 'TrailerUpdated';

    // Beta Events - Trips
    case TRIP_COMPLETED = 'TripCompleted';
    case TRIP_STARTED = 'TripStarted';

    // Beta Events - Unassigned Driving
    case UNASSIGNED_DRIVING_CREATED = 'UnassignedDrivingCreated';

    // Beta Events - Users
    case USER_CREATED = 'UserCreated';
    case USER_DELETED = 'UserDeleted';
    case USER_UPDATED = 'UserUpdated';

    // Beta Events - Vehicles
    case VEHICLE_CREATED = 'VehicleCreated';
    case VEHICLE_DELETED = 'VehicleDeleted';
    case VEHICLE_UPDATED = 'VehicleUpdated';

    // Beta Events - Work Orders
    case WORK_ORDER_CREATED = 'WorkOrderCreated';
    case WORK_ORDER_UPDATED = 'WorkOrderUpdated';

    /**
     * Get all webhook event values as an array.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return self::values()->all();
    }

    /**
     * Get a collection of all webhook event cases.
     *
     * @return Collection<int, self>
     */
    public static function collect(): Collection
    {
        return new Collection(self::cases());
    }

    /**
     * Get a collection of all webhook event values.
     *
     * @return Collection<int, string>
     */
    public static function values(): Collection
    {
        return self::collect()->map(fn (self $case) => $case->value);
    }
}

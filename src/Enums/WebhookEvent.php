<?php

namespace ErikGall\Samsara\Enums;

use Illuminate\Support\Collection;

/**
 * Webhook event type enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum WebhookEvent: string
{
    case ADDRESS_CREATED = 'AddressCreated';
    case ADDRESS_DELETED = 'AddressDeleted';
    case ADDRESS_UPDATED = 'AddressUpdated';
    case ALERT_INCIDENT_CREATED = 'AlertIncidentCreated';
    case ALERT_INCIDENT_RESOLVED = 'AlertIncidentResolved';
    case ASSET_CREATED = 'AssetCreated';
    case ASSET_DELETED = 'AssetDeleted';
    case ASSET_UPDATED = 'AssetUpdated';
    case COACHING_SESSION_CREATED = 'CoachingSessionCreated';
    case COACHING_SESSION_UPDATED = 'CoachingSessionUpdated';
    case DOCUMENT_SUBMITTED = 'DocumentSubmitted';
    case DRIVER_CREATED = 'DriverCreated';
    case DRIVER_DELETED = 'DriverDeleted';
    case DRIVER_UPDATED = 'DriverUpdated';
    case DVIR_DEFECT_RESOLVED = 'DvirDefectResolved';
    case DVIR_SUBMITTED = 'DvirSubmitted';
    case ENGINE_FAULT_OFF = 'EngineFaultOff';
    case ENGINE_FAULT_ON = 'EngineFaultOn';
    case EQUIPMENT_CREATED = 'EquipmentCreated';
    case EQUIPMENT_DELETED = 'EquipmentDeleted';
    case EQUIPMENT_UPDATED = 'EquipmentUpdated';
    case FORM_SUBMITTED = 'FormSubmitted';
    case FORM_UPDATED = 'FormUpdated';
    case GATEWAY_HEARTBEAT = 'GatewayHeartbeat';
    case GATEWAY_UNPLUGGED = 'GatewayUnplugged';
    case GEOFENCE_ENTRY = 'GeofenceEntry';
    case GEOFENCE_EXIT = 'GeofenceExit';
    case HOS_CLOCKS_UPDATED = 'HosClocksUpdated';
    case HOS_LOGS_UPDATED = 'HosLogsUpdated';
    case HOS_VIOLATION = 'HosViolation';
    case ISSUE_CREATED = 'IssueCreated';
    case ISSUE_UPDATED = 'IssueUpdated';
    case LIVE_SHARE_CREATED = 'LiveShareCreated';
    case LIVE_SHARE_EXPIRED = 'LiveShareExpired';
    case PANIC_BUTTON_PRESSED = 'PanicButtonPressed';
    case PREDICTIVE_MAINTENANCE_ALERT = 'PredictiveMaintenanceAlert';
    case ROUTE_CREATED = 'RouteCreated';
    case ROUTE_DELETED = 'RouteDeleted';
    case ROUTE_STOP_ARRIVAL = 'RouteStopArrival';
    case ROUTE_STOP_DEPARTURE = 'RouteStopDeparture';
    case ROUTE_STOP_EARLY_LATE_ARRIVAL = 'RouteStopEarlyLateArrival';
    case ROUTE_STOP_ETA_UPDATED = 'RouteStopEtaUpdated';
    case ROUTE_STOP_RESEQUENCE = 'RouteStopResequence';
    case ROUTE_UPDATED = 'RouteUpdated';
    case SAFETY_EVENT_CREATED = 'SafetyEventCreated';
    case SEVERE_SPEEDING_ENDED = 'SevereSpeedingEnded';
    case SEVERE_SPEEDING_STARTED = 'SevereSpeedingStarted';
    case SPEEDING_EVENT_ENDED = 'SpeedingEventEnded';
    case SPEEDING_EVENT_STARTED = 'SpeedingEventStarted';
    case TAG_CREATED = 'TagCreated';
    case TAG_DELETED = 'TagDeleted';
    case TAG_UPDATED = 'TagUpdated';
    case TRAILER_CREATED = 'TrailerCreated';
    case TRAILER_DELETED = 'TrailerDeleted';
    case TRAILER_UPDATED = 'TrailerUpdated';
    case TRIP_COMPLETED = 'TripCompleted';
    case TRIP_STARTED = 'TripStarted';
    case UNASSIGNED_DRIVING_CREATED = 'UnassignedDrivingCreated';
    case USER_CREATED = 'UserCreated';
    case USER_DELETED = 'UserDeleted';
    case USER_UPDATED = 'UserUpdated';
    case VEHICLE_CREATED = 'VehicleCreated';
    case VEHICLE_DELETED = 'VehicleDeleted';
    case VEHICLE_UPDATED = 'VehicleUpdated';
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

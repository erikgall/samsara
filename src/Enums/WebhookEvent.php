<?php

namespace Samsara\Enums;

use Illuminate\Support\Collection;

/**
 * Webhook event type enum.
 *
 * These are the valid event types for Samsara webhook subscriptions.
 *
 * @see https://developers.samsara.com/docs/event-subscriptions
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum WebhookEvent: string
{
    case ADDRESS_CREATED = 'AddressCreated';
    case ADDRESS_DELETED = 'AddressDeleted';
    case ADDRESS_UPDATED = 'AddressUpdated';
    case DOCUMENT_SUBMITTED = 'DocumentSubmitted';
    case DRIVER_CREATED = 'DriverCreated';
    case DRIVER_UPDATED = 'DriverUpdated';
    case DVIR_SUBMITTED = 'DvirSubmitted';
    case ENGINE_FAULT_OFF = 'EngineFaultOff';
    case ENGINE_FAULT_ON = 'EngineFaultOn';
    case FORM_SUBMITTED = 'FormSubmitted';
    case FORM_UPDATED = 'FormUpdated';
    case GATEWAY_UNPLUGGED = 'GatewayUnplugged';
    case GEOFENCE_ENTRY = 'GeofenceEntry';
    case GEOFENCE_EXIT = 'GeofenceExit';
    case ISSUE_CREATED = 'IssueCreated';
    case PREDICTIVE_MAINTENANCE_ALERT = 'PredictiveMaintenanceAlert';
    case ROUTE_STOP_ARRIVAL = 'RouteStopArrival';
    case ROUTE_STOP_DEPARTURE = 'RouteStopDeparture';
    case ROUTE_STOP_EARLY_LATE_ARRIVAL = 'RouteStopEarlyLateArrival';
    case ROUTE_STOP_ETA_UPDATED = 'RouteStopEtaUpdated';
    case ROUTE_STOP_RESEQUENCE = 'RouteStopResequence';
    case SEVERE_SPEEDING_ENDED = 'SevereSpeedingEnded';
    case SEVERE_SPEEDING_STARTED = 'SevereSpeedingStarted';
    case SPEEDING_EVENT_ENDED = 'SpeedingEventEnded';
    case SPEEDING_EVENT_STARTED = 'SpeedingEventStarted';
    case VEHICLE_CREATED = 'VehicleCreated';
    case VEHICLE_UPDATED = 'VehicleUpdated';

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

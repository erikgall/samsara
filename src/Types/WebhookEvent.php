<?php

namespace ErikGall\Samsara\Types;

use Illuminate\Support\Collection;

/**
 * Webhook event type definitions.
 *
 * @author Erik Galloway <egalloway@boltsystem.com>
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
    case ROUTE_STOP_RESEQUENCE = 'RouteStopResequence';
    case SEVERE_SPEEDING_ENDED = 'SevereSpeedingEnded';
    case SEVERE_SPEEDING_STARTED = 'SevereSpeedingStarted';
    case VEHICLE_CREATED = 'VehicleCreated';
    case VEHICLE_UPDATED = 'VehicleUpdated';

    /**
     * Get all webhook event types as an array.
     *
     * @return array
     */
    public static function all()
    {
        return self::values()->all();
    }

    /**
     * Get a collection of all webhook event types.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function collect()
    {
        return new Collection(self::cases());
    }

    /**
     * Get a collection of all webhook event type values.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function values()
    {
        return self::collect()->map->value;
    }
}

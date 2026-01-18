<?php

namespace ErikGall\Samsara\Enums;

/**
 * Alert type enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum AlertType: string
{
    case BATTERY = 'battery';
    case CARGO = 'cargo';
    case CUSTOM = 'custom';
    case DOOR = 'door';
    case ENGINE_FAULT = 'engineFault';
    case FUEL = 'fuel';
    case GEOFENCE = 'geofence';
    case HOS_VIOLATION = 'hosViolation';
    case HUMIDITY = 'humidity';
    case IDLE = 'idle';
    case MAINTENANCE = 'maintenance';
    case PANIC = 'panic';
    case POWER = 'power';
    case SAFETY_EVENT = 'safetyEvent';
    case SPEEDING = 'speeding';
    case TEMPERATURE = 'temperature';
}

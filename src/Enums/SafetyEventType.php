<?php

namespace Samsara\Enums;

/**
 * Safety event behavior label type enum.
 *
 * Samsara calls this schema SafetyEventBehaviorLabelType. The historical PHP
 * class name remains unchanged for compatibility.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum SafetyEventType: string
{
    case ACCELERATION = 'acceleration';
    case BRAKING = 'braking';
    case CRASH = 'crash';
    case DEFENSIVE_DRIVING = 'defensiveDriving';
    case DID_NOT_YIELD = 'didNotYield';
    case DROWSY = 'drowsy';
    case EATING_DRINKING = 'eatingDrinking';
    case EDGE_DISTRACTED_DRIVING = 'edgeDistractedDriving';
    case FOLLOWING_DISTANCE = 'followingDistance';
    case FOLLOWING_DISTANCE_MODERATE = 'followingDistanceModerate';
    case FOLLOWING_DISTANCE_SEVERE = 'followingDistanceSevere';
    case FORWARD_COLLISION_WARNING = 'forwardCollisionWarning';
    case GENERIC_DISTRACTION = 'genericDistraction';
    case GENERIC_TAILGATING = 'genericTailgating';
    case HARSH_TURN = 'harshTurn';
    case LANE_DEPARTURE = 'laneDeparture';
    case LATE_RESPONSE = 'lateResponse';
    case MOBILE_USAGE = 'mobileUsage';
    case NEAR_COLLISION = 'nearCollison';
    case NO_SEATBELT = 'noSeatbelt';
    case OBSTRUCTED_CAMERA = 'obstructedCamera';
    case RAN_RED_LIGHT = 'ranRedLight';
    case ROLLING_STOP = 'rollingStop';
    case ROLLOVER_PROTECTION = 'rolloverProtection';
    case SMOKING = 'smoking';
    case SPEEDING = 'speeding';
    case YAW_CONTROL = 'yawControl';
}

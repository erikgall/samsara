<?php

namespace Samsara\Enums;

/**
 * Safety event type enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum SafetyEventType: string
{
    case CAMERA_OBSTRUCTION = 'cameraObstruction';
    case CELL_PHONE_USAGE = 'cellPhoneUsage';
    case CRASH = 'crash';
    case DEFENSIVE_DRIVING = 'defensiveDriving';
    case DISTRACTED_DRIVING = 'distractedDriving';
    case DRIVER_DETECTED = 'driverDetected';
    case DROWSY_DRIVING = 'drowsyDriving';
    case FOLLOWING_DISTANCE = 'followingDistance';
    case HARSH_ACCELERATION = 'harshAcceleration';
    case HARSH_BRAKING = 'harshBraking';
    case HARSH_TURN = 'harshTurn';
    case LANE_DEPARTURE = 'laneDeparture';
    case MAX_SPEED = 'maxSpeed';
    case NEAR_COLLISION = 'nearCollision';
    case NO_DRIVER_DETECTED = 'noDriverDetected';
    case ROLLING_STOP = 'rollingStop';
    case SEATBELT = 'seatbelt';
    case SMOKING = 'smoking';
    case SPEEDING = 'speeding';
    case UNKNOWN = 'unknown';
}

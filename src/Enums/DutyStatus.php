<?php

namespace Samsara\Enums;

/**
 * HOS duty status enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum DutyStatus: string
{
    case DRIVING = 'driving';
    case OFF_DUTY = 'offDuty';
    case ON_DUTY = 'onDuty';
    case PERSONAL_CONVEYANCE = 'personalConveyance';
    case SLEEPER_BERTH = 'sleeperBerth';
    case YARD_MOVE = 'yardMove';
}

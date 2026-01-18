<?php

namespace Samsara\Enums;

/**
 * Driver activation status enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum DriverActivationStatus: string
{
    case ACTIVE = 'active';
    case DEACTIVATED = 'deactivated';
}

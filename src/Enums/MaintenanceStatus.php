<?php

namespace Samsara\Enums;

/**
 * Maintenance status enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum MaintenanceStatus: string
{
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'inProgress';
    case OPEN = 'open';
}

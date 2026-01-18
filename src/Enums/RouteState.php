<?php

namespace ErikGall\Samsara\Enums;

/**
 * Route state enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum RouteState: string
{
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'inProgress';
    case NOT_STARTED = 'notStarted';
    case SKIPPED = 'skipped';
}

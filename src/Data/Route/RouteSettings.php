<?php

namespace ErikGall\Samsara\Data\Route;

use ErikGall\Samsara\Data\Entity;

/**
 * RouteSettings entity.
 *
 * Represents settings for a Samsara route.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $routeCompletionCondition Route completion condition (arriveLastStop, departLastStop)
 * @property-read string|null $routeStartingCondition Route starting condition (departFirstStop, arriveFirstStop)
 * @property-read string|null $sequencingMethod Sequencing method (unknown, scheduledArrivalTime, manual)
 */
class RouteSettings extends Entity
{
    /**
     * Check if the route completes when arriving at the last stop.
     */
    public function completesOnArriveLastStop(): bool
    {
        return $this->routeCompletionCondition === 'arriveLastStop';
    }

    /**
     * Check if the route completes when departing from the last stop.
     */
    public function completesOnDepartLastStop(): bool
    {
        return $this->routeCompletionCondition === 'departLastStop';
    }

    /**
     * Check if the sequencing is by scheduled arrival time.
     */
    public function isSequencedByScheduledTime(): bool
    {
        return $this->sequencingMethod === 'scheduledArrivalTime';
    }

    /**
     * Check if the sequencing is manual.
     */
    public function isSequencedManually(): bool
    {
        return $this->sequencingMethod === 'manual';
    }

    /**
     * Check if the route starts when arriving at the first stop.
     */
    public function startsOnArriveFirstStop(): bool
    {
        return $this->routeStartingCondition === 'arriveFirstStop';
    }

    /**
     * Check if the route starts when departing from the first stop.
     */
    public function startsOnDepartFirstStop(): bool
    {
        return $this->routeStartingCondition === 'departFirstStop';
    }
}

<?php

namespace Samsara\Data\Route;

use Samsara\Data\Entity;

/**
 * RouteStop entity.
 *
 * Represents a stop on a Samsara route.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Stop ID (UUID)
 * @property-read string|null $name Stop name
 * @property-read string|null $hubLocationId Hub location identifier
 * @property-read string|null $notes Additional notes for the stop
 * @property-read string|null $scheduledArrivalTime Scheduled arrival time
 * @property-read string|null $scheduledDepartureTime Scheduled departure time
 * @property-read array<int, array{id?: string, description?: string}>|null $orders Order tasks
 * @property-read array{address?: string, latitude?: float, longitude?: float}|null $singleUseLocation Single-use location
 */
class RouteStop extends Entity
{
    /**
     * Get the display name for the stop.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? 'Unknown';
    }

    /**
     * Get the number of orders at this stop.
     */
    public function getOrderCount(): int
    {
        $orders = $this->orders ?? [];

        return count($orders);
    }

    /**
     * Check if this stop has a hub location.
     */
    public function hasHubLocation(): bool
    {
        return ! empty($this->hubLocationId);
    }

    /**
     * Check if this stop has a single-use location.
     */
    public function hasSingleUseLocation(): bool
    {
        return ! empty($this->singleUseLocation);
    }
}

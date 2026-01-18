<?php

namespace Samsara\Data\Route;

use Samsara\Data\Entity;

/**
 * Route entity.
 *
 * Represents a Samsara route.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Route ID (UUID)
 * @property-read string|null $name Route name
 * @property-read string|null $type Route type (e.g., 'dynamic')
 * @property-read string|null $hubId Hub ID this route belongs to
 * @property-read string|null $planId Plan ID this route belongs to
 * @property-read string|null $dispatchRouteId Dispatch route identifier
 * @property-read float|null $cost Route cost
 * @property-read int|null $distanceMeters Total distance in meters
 * @property-read int|null $durationSeconds Total duration in seconds
 * @property-read string|null $orgLocationTimezone Organization location timezone
 * @property-read string|null $scheduledRouteStartTime Scheduled start time
 * @property-read string|null $scheduledRouteEndTime Scheduled end time
 * @property-read string|null $createdAt Creation timestamp
 * @property-read string|null $updatedAt Last update timestamp
 * @property-read bool|null $isEdited Whether the route has been edited
 * @property-read bool|null $isPinned Whether the route is pinned
 * @property-read array<int, array{id: string, name?: string, hubLocationId?: string, notes?: string, scheduledArrivalTime?: string, scheduledDepartureTime?: string, orders?: array<int, mixed>, singleUseLocation?: array<string, mixed>}>|null $stops Route stops
 * @property-read array<int, array{name?: string, value?: float|int}>|null $quantities Route quantities
 * @property-read array{routeCompletionCondition?: string, routeStartingCondition?: string, sequencingMethod?: string}|null $settings Route settings
 */
class Route extends Entity
{
    /**
     * Meters per mile conversion constant.
     */
    protected const METERS_PER_MILE = 1609.344;

    /**
     * Get the display name for the route.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? 'Unknown';
    }

    /**
     * Get the distance in kilometers.
     */
    public function getDistanceKilometers(): ?float
    {
        if ($this->distanceMeters === null) {
            return null;
        }

        return $this->distanceMeters / 1000;
    }

    /**
     * Get the distance in miles.
     */
    public function getDistanceMiles(): ?float
    {
        if ($this->distanceMeters === null) {
            return null;
        }

        return $this->distanceMeters / self::METERS_PER_MILE;
    }

    /**
     * Get the duration in hours.
     */
    public function getDurationHours(): ?float
    {
        if ($this->durationSeconds === null) {
            return null;
        }

        return $this->durationSeconds / 3600;
    }

    /**
     * Get the duration in minutes.
     */
    public function getDurationMinutes(): ?float
    {
        if ($this->durationSeconds === null) {
            return null;
        }

        return $this->durationSeconds / 60;
    }

    /**
     * Get the route settings as an entity.
     */
    public function getSettings(): ?RouteSettings
    {
        if (empty($this->settings)) {
            return null;
        }

        return new RouteSettings($this->settings);
    }

    /**
     * Get the number of stops on this route.
     */
    public function getStopCount(): int
    {
        $stops = $this->stops ?? [];

        return count($stops);
    }

    /**
     * Get the stops as RouteStop entities.
     *
     * @return array<int, RouteStop>
     */
    public function getStops(): array
    {
        $stops = $this->stops ?? [];

        return array_map(fn (array $stop): RouteStop => new RouteStop($stop), $stops);
    }

    /**
     * Check if the route has been edited.
     */
    public function isEdited(): bool
    {
        return $this->isEdited ?? false;
    }

    /**
     * Check if the route is pinned.
     */
    public function isPinned(): bool
    {
        return $this->isPinned ?? false;
    }
}

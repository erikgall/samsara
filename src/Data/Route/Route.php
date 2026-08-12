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
 * @property-read string|null $id Route ID
 * @property-read string|null $name Route name
 * @property-read string|null $notes Route notes
 * @property-read string|null $orgLocalTimezone Organization-local timezone
 * @property-read string|null $actualRouteStartTime Actual route start time
 * @property-read string|null $actualRouteEndTime Actual route end time
 * @property-read string|null $scheduledRouteStartTime Scheduled start time
 * @property-read string|null $scheduledRouteEndTime Scheduled end time
 * @property-read array{id?: string, name?: string}|null $driver Assigned driver
 * @property-read array{id?: string, name?: string}|null $vehicle Assigned vehicle
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, string>|null $tagIds Tag IDs
 * @property-read array<int, array<string, mixed>>|null $recurringRouteLiveSharingLinks Live sharing links
 * @property-read array<int, array<string, mixed>>|null $stops Route stops
 * @property-read array{routeCompletionCondition?: string, routeStartingCondition?: string, sequencingMethod?: string}|null $settings Route settings
 */
class Route extends Entity
{
    /**
     * Get the display name for the route.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? 'Unknown';
    }

    /**
     * Get an external ID by key.
     */
    public function getExternalId(string $key): ?string
    {
        $externalIds = $this->get('externalIds');

        if (! is_array($externalIds)) {
            return null;
        }

        $externalId = $externalIds[$key] ?? null;

        return is_string($externalId) ? $externalId : null;
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
}

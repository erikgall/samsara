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
 * @property-read string|null $id Stop ID
 * @property-read string|null $name Stop name
 * @property-read string|null $state Stop state
 * @property-read string|null $notes Additional notes for the stop
 * @property-read array<string, mixed>|null $address Stop address
 * @property-read array<int, array<string, mixed>>|null $appointmentWindows Appointment windows
 * @property-read array<int, array<string, mixed>>|null $documents Stop documents
 * @property-read string|null $enRouteTime En-route time
 * @property-read string|null $eta Estimated arrival time
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array<string, mixed>>|null $forms Stop forms
 * @property-read array<int, array<string, mixed>>|null $issues Stop issues
 * @property-read string|null $liveSharingUrl Stop live sharing URL
 * @property-read array<int, array<string, mixed>>|null $locationLiveSharingLinks Location live sharing links
 * @property-read int|null $ontimeWindowAfterArrivalMs On-time window after arrival
 * @property-read int|null $ontimeWindowBeforeArrivalMs On-time window before arrival
 * @property-read int|null $actualDistanceMeters Actual distance from the prior stop
 * @property-read int|null $plannedDistanceMeters Planned distance from the prior stop
 * @property-read int|null $sequenceNumber Stop sequence number
 * @property-read string|null $skippedTime Skipped time
 * @property-read string|null $actualArrivalTime Actual arrival time
 * @property-read string|null $actualDepartureTime Actual departure time
 * @property-read string|null $scheduledArrivalTime Scheduled arrival time
 * @property-read string|null $scheduledDepartureTime Scheduled departure time
 * @property-read array<int, array<string, mixed>>|null $orders Order tasks
 * @property-read array<string, mixed>|null $singleUseLocation Single-use location
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
     * Check if this stop has a single-use location.
     */
    public function hasSingleUseLocation(): bool
    {
        return ! empty($this->singleUseLocation);
    }
}

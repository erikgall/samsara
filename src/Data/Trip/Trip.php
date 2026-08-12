<?php

namespace Samsara\Data\Trip;

use Samsara\Data\Entity;

/**
 * Trip entity.
 *
 * Represents a vehicle trip.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $tripStartTime Trip start time (RFC 3339)
 * @property-read string|null $tripEndTime Trip end time (RFC 3339)
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 * @property-read string|null $completionStatus Trip completion status
 * @property-read array{id?: string, name?: string}|null $asset Associated asset
 * @property-read array{latitude?: float, longitude?: float}|null $startLocation Trip start location
 * @property-read array{latitude?: float, longitude?: float}|null $endLocation Trip end location
 */
class Trip extends Entity
{
    /**
     * Check if the trip is completed.
     */
    public function isCompleted(): bool
    {
        return $this->completionStatus === 'completed';
    }

    /**
     * Check if the trip is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->completionStatus === 'inProgress';
    }
}

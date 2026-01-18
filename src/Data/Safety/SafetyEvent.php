<?php

namespace ErikGall\Samsara\Data\Safety;

use ErikGall\Samsara\Data\Entity;

/**
 * SafetyEvent entity.
 *
 * Represents a Samsara safety event.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Safety event ID
 * @property-read string|null $time Event time (RFC 3339)
 * @property-read string|null $coachingState Coaching state
 * @property-read float|null $maxAccelerationGForce Maximum acceleration in G-force
 * @property-read string|null $downloadForwardVideoUrl Forward video URL
 * @property-read string|null $downloadInwardVideoUrl Inward video URL
 * @property-read string|null $downloadTrackedInwardVideoUrl Tracked inward video URL
 * @property-read array{id?: string, name?: string}|null $driver Driver information
 * @property-read array{id?: string, name?: string}|null $vehicle Vehicle information
 * @property-read array{latitude?: float, longitude?: float}|null $location Event location
 * @property-read array<int, array{label?: string, source?: string}>|null $behaviorLabels Behavior labels
 */
class SafetyEvent extends Entity
{
    /**
     * Check if the event has video.
     */
    public function hasVideo(): bool
    {
        return ! empty($this->downloadForwardVideoUrl)
            || ! empty($this->downloadInwardVideoUrl)
            || ! empty($this->downloadTrackedInwardVideoUrl);
    }
}

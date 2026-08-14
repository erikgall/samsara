<?php

namespace Samsara\Data\Maintenance;

use Samsara\Data\Entity;

/**
 * Dvir entity.
 *
 * Represents a Driver Vehicle Inspection Report.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id DVIR ID
 * @property-read string|null $type Inspection type (preTrip, postTrip, mechanic, unspecified)
 * @property-read string|null $safetyStatus Safety status (safe, unsafe, resolved)
 * @property-read string|null $startTime Start time (RFC 3339)
 * @property-read string|null $endTime End time (RFC 3339)
 * @property-read string|null $dvirSubmissionBeginTime Stream submission start time (RFC 3339)
 * @property-read string|null $dvirSubmissionTime Stream submission time (RFC 3339)
 * @property-read string|null $updatedAtTime Stream update time (RFC 3339)
 * @property-read array<int, string>|null $defectIds Stream defect IDs
 * @property-read string|null $formattedAddress Stream location address
 * @property-read int|null $odometerMeters Odometer reading in meters
 * @property-read string|null $licensePlate License plate
 * @property-read string|null $mechanicNotes Mechanic notes
 * @property-read string|null $trailerName Trailer name
 * @property-read array{id?: string, name?: string}|null $vehicle Vehicle information
 * @property-read array{id?: string, name?: string}|null $trailer Trailer information
 * @property-read string|null $location Formatted inspection location
 * @property-read array{signedAtTime?: string}|null $authorSignature Author signature
 * @property-read array{signedAtTime?: string}|null $secondSignature Second signature
 * @property-read array{signedAtTime?: string}|null $thirdSignature Third signature
 * @property-read array<int, array{id?: string, defectType?: string}>|null $vehicleDefects Vehicle defects
 * @property-read array<int, array{id?: string, defectType?: string}>|null $trailerDefects Trailer defects
 * @property-read array<int, array<string, mixed>>|null $walkaroundPhotos Stream walkaround photos
 */
class Dvir extends Entity
{
    /**
     * Check if the DVIR has any defects.
     */
    public function hasDefects(): bool
    {
        foreach (['defectIds', 'vehicleDefects', 'trailerDefects'] as $field) {
            $defects = $this->get($field);

            if (is_array($defects) && $defects !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if this is a mechanic inspection.
     */
    public function isMechanicInspection(): bool
    {
        return $this->type === 'mechanic';
    }

    /**
     * Check if this is a post-trip inspection.
     */
    public function isPostTrip(): bool
    {
        return $this->type === 'postTrip';
    }

    /**
     * Check if this is a pre-trip inspection.
     */
    public function isPreTrip(): bool
    {
        return $this->type === 'preTrip';
    }

    /**
     * Check if the DVIR has been resolved.
     */
    public function isResolved(): bool
    {
        return $this->safetyStatus === 'resolved';
    }

    /**
     * Check if the vehicle is safe.
     */
    public function isSafe(): bool
    {
        return $this->safetyStatus === 'safe';
    }

    /**
     * Check if the vehicle is unsafe.
     */
    public function isUnsafe(): bool
    {
        return $this->safetyStatus === 'unsafe';
    }
}

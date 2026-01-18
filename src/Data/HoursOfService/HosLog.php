<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Enums\DutyStatus;

/**
 * HosLog entity.
 *
 * Represents a single HOS log entry.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $hosStatusType HOS status type (offDuty, sleeperBed, driving, onDuty, yardMove, personalConveyance)
 * @property-read string|null $logStartTime Log start time (RFC 3339)
 * @property-read string|null $logEndTime Log end time (RFC 3339)
 * @property-read string|null $remark Remark associated with the log entry
 * @property-read array{id?: string, name?: string}|null $vehicle Vehicle information
 * @property-read array<int, array{id?: string, name?: string}>|null $codrivers Codriver information
 * @property-read array{latitude?: float, longitude?: float, location?: string}|null $logRecordedLocation Location where log was recorded
 */
class HosLog extends Entity
{
    /**
     * Get the duty status as an enum.
     */
    public function getDutyStatus(): ?DutyStatus
    {
        if (empty($this->hosStatusType)) {
            return null;
        }

        return DutyStatus::tryFrom($this->hosStatusType);
    }

    /**
     * Check if the log entry is for driving.
     */
    public function isDriving(): bool
    {
        return $this->hosStatusType === 'driving';
    }

    /**
     * Check if the log entry is for off duty.
     */
    public function isOffDuty(): bool
    {
        return $this->hosStatusType === 'offDuty';
    }

    /**
     * Check if the log entry is for on duty.
     */
    public function isOnDuty(): bool
    {
        return $this->hosStatusType === 'onDuty';
    }

    /**
     * Check if the log entry is for personal conveyance.
     */
    public function isPersonalConveyance(): bool
    {
        return $this->hosStatusType === 'personalConveyance';
    }

    /**
     * Check if the log entry is for sleeper berth.
     */
    public function isSleeperBerth(): bool
    {
        return $this->hosStatusType === 'sleeperBed';
    }

    /**
     * Check if the log entry is for yard move.
     */
    public function isYardMove(): bool
    {
        return $this->hosStatusType === 'yardMove';
    }
}

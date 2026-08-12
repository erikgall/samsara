<?php

namespace Samsara\Data\Industrial;

use DateTimeImmutable;
use Samsara\Data\Entity;

/**
 * DataPoint entity.
 *
 * Represents a data point reading.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $time Timestamp (RFC 3339)
 * @property-read mixed $value Numeric, string, or J1939 status value
 * @property-read array<string, mixed>|null $fftSpectra FFT spectrum data
 * @property-read array{latitude?: float, longitude?: float}|null $gpsLocation GPS location data
 */
class DataPoint extends Entity
{
    /**
     * Get the time at which the point was recorded.
     */
    public function getRecordedAt(): ?DateTimeImmutable
    {
        $time = $this->get('time');

        if (! is_string($time) || $time === '') {
            return null;
        }

        return new DateTimeImmutable($time);
    }
}

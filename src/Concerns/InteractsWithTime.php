<?php

namespace Samsara\Concerns;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * Trait for date/time formatting.
 *
 * Samsara API uses RFC 3339 format for timestamps in UTC (e.g., 2024-01-15T14:30:00Z).
 *
 * @see https://developers.samsara.com/docs/timestamps
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
trait InteractsWithTime
{
    /**
     * Format a time value for API requests.
     *
     * Converts DateTime objects to RFC 3339 format in UTC with Z suffix.
     * String values are returned as-is (assumed to be properly formatted).
     *
     * @param  DateTimeInterface|string  $time  The time to format
     * @return string RFC 3339 formatted timestamp (e.g., 2024-01-15T14:30:00Z)
     */
    protected function formatTime(DateTimeInterface|string $time): string
    {
        if ($time instanceof DateTimeInterface) {
            return $this->toUtcString($time);
        }

        return $time;
    }

    /**
     * Convert a DateTime to RFC 3339 UTC string with Z suffix.
     *
     * @param  DateTimeInterface  $time  The time to convert
     * @return string RFC 3339 formatted timestamp (e.g., 2024-01-15T14:30:00Z)
     */
    protected function toUtcString(DateTimeInterface $time): string
    {
        $carbon = $time instanceof Carbon ? $time->copy() : Carbon::instance($time);

        return $carbon->utc()->format('Y-m-d\TH:i:s\Z');
    }
}

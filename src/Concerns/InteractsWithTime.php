<?php

namespace Samsara\Concerns;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * Trait for date/time formatting and parsing.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @phpstan-ignore trait.unused
 */
trait InteractsWithTime
{
    /**
     * Format a time value for API requests.
     *
     * Returns RFC 3339 formatted string for DateTime objects,
     * or returns the string as-is.
     */
    protected function formatTime(DateTimeInterface|string $time): string
    {
        if ($time instanceof DateTimeInterface) {
            return $time->format(DateTimeInterface::RFC3339);
        }

        return $time;
    }

    /**
     * Parse a time value into a Carbon instance.
     */
    protected function parseTime(DateTimeInterface|string $time): Carbon
    {
        if ($time instanceof Carbon) {
            return $time;
        }

        if ($time instanceof DateTimeInterface) {
            return Carbon::instance($time);
        }

        return Carbon::parse($time);
    }
}

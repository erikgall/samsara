<?php

namespace Samsara\Concerns;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * Trait for date/time formatting and parsing.
 *
 * Samsara API uses RFC 3339 format for timestamps in UTC (e.g., 2024-01-15T14:30:00Z).
 * Legacy v1 endpoints use Unix timestamps in milliseconds.
 *
 * @see https://developers.samsara.com/docs/timestamps
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
     * Convert a Unix timestamp in milliseconds to a Carbon instance.
     *
     * Used for parsing timestamps from legacy v1 API responses.
     *
     * @param  int  $milliseconds  Unix timestamp in milliseconds
     */
    protected function fromUnixMilliseconds(int $milliseconds): Carbon
    {
        return Carbon::createFromTimestampMs($milliseconds);
    }

    /**
     * Parse a time value into a Carbon instance.
     *
     * Accepts Carbon instances, DateTimeInterface objects, or RFC 3339 strings.
     *
     * @param  DateTimeInterface|string  $time  The time to parse
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

    /**
     * Convert a DateTime to Unix timestamp in milliseconds.
     *
     * Used for formatting timestamps for legacy v1 API requests.
     *
     * @param  DateTimeInterface|string  $time  The time to convert
     * @return int Unix timestamp in milliseconds
     */
    protected function toUnixMilliseconds(DateTimeInterface|string $time): int
    {
        $carbon = $this->parseTime($time);

        return (int) $carbon->valueOf();
    }

    /**
     * Convert a DateTime to RFC 3339 UTC string with Z suffix.
     *
     * @param  DateTimeInterface  $time  The time to convert
     * @return string RFC 3339 formatted timestamp (e.g., 2024-01-15T14:30:00Z)
     */
    protected function toUtcString(DateTimeInterface $time): string
    {
        $carbon = $time instanceof Carbon ? $time : Carbon::instance($time);

        return $carbon->utc()->format('Y-m-d\TH:i:s\Z');
    }
}

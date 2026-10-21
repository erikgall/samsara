<?php

namespace Samsara\Http;

use DateTimeZone;
use DateTimeImmutable;
use Illuminate\Support\Carbon;

/**
 * @internal
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
final class RetryAfter
{
    public static function seconds(?string $header): ?int
    {
        if ($header === null || str_contains($header, "\0")) {
            return null;
        }

        $header = trim($header);

        if (ctype_digit($header)) {
            return (int) $header;
        }

        $format = 'D, d M Y H:i:s \\G\\M\\T';
        $date = DateTimeImmutable::createFromFormat($format, $header, new DateTimeZone('UTC'));

        if ($date === false || $date->format($format) !== $header) {
            return null;
        }

        return max(0, $date->getTimestamp() - Carbon::now()->getTimestamp());
    }
}

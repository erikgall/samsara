<?php

namespace Samsara\Http;

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
        if ($header === null) {
            return null;
        }

        $header = trim($header);

        if (ctype_digit($header)) {
            return (int) $header;
        }

        $timestamp = strtotime($header);

        return $timestamp === false ? null : max(0, $timestamp - Carbon::now()->getTimestamp());
    }
}

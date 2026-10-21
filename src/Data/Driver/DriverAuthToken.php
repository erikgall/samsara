<?php

namespace Samsara\Data\Driver;

use Samsara\Data\Entity;
use Illuminate\Support\Carbon;

/**
 * Driver authentication token result.
 *
 * @property-read int|null $expirationTime Expiration time in milliseconds since the Unix epoch
 * @property-read string|null $token Driver authentication token
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriverAuthToken extends Entity
{
    /**
     * Check whether the one-time token has expired.
     */
    public function isExpired(): bool
    {
        $expirationTime = $this->get('expirationTime');

        if (! is_int($expirationTime)) {
            return false;
        }

        return $expirationTime <= (int) floor(Carbon::now()->getPreciseTimestamp() / 1000);
    }
}

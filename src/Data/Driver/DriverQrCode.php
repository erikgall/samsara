<?php

namespace Samsara\Data\Driver;

use Samsara\Data\Entity;

/**
 * Driver QR code result.
 *
 * @property-read int|null $driverId Driver ID
 * @property-read string|null $qrCodeLink Driver QR code URL
 */
class DriverQrCode extends Entity
{
    /**
     * Check whether a QR code link has been created.
     */
    public function hasQrCode(): bool
    {
        $link = $this->get('qrCodeLink');

        return is_string($link) && $link !== '';
    }
}

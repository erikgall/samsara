<?php

namespace Samsara\Data\Driver;

use Samsara\Data\Entity;

/**
 * Driver carrier settings entity.
 *
 * Represents the carrier settings for a driver.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $carrierName Carrier name
 * @property-read int|null $dotNumber US DOT Number
 * @property-read string|null $homeTerminalAddress Home terminal address
 * @property-read string|null $homeTerminalName Home terminal name
 * @property-read string|null $mainOfficeAddress Main office address
 */
class CarrierSettings extends Entity
{
    /**
     * Get the carrier name.
     */
    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    /**
     * Get the DOT number.
     */
    public function getDotNumber(): ?int
    {
        return $this->dotNumber;
    }
}

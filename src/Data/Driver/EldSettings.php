<?php

namespace ErikGall\Samsara\Data\Driver;

use ErikGall\Samsara\Data\Entity;

/**
 * Driver ELD settings entity.
 *
 * Represents the ELD (Electronic Logging Device) settings for a driver.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read array<int, array{name?: string, cycle?: string}>|null $rulesets ELD rulesets
 */
class EldSettings extends Entity
{
    /**
     * Get the ELD rulesets.
     *
     * @return array<int, array{name?: string, cycle?: string}>
     */
    public function getRulesets(): array
    {
        return $this->rulesets ?? [];
    }

    /**
     * Check if the driver has any rulesets configured.
     */
    public function hasRulesets(): bool
    {
        return ! empty($this->rulesets);
    }
}

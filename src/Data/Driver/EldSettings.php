<?php

namespace Samsara\Data\Driver;

use Samsara\Data\Entity;

/**
 * Driver ELD settings entity.
 *
 * Represents the ELD (Electronic Logging Device) settings for a driver.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read array<int, array{break?: string, cycle?: string, jurisdiction?: string, restart?: string, shift?: string}>|null $rulesets ELD rulesets
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
        $rulesets = $this->get('rulesets');

        return is_array($rulesets) ? $rulesets : [];
    }

    /**
     * Check if the driver has any rulesets configured.
     */
    public function hasRulesets(): bool
    {
        return $this->getRulesets() !== [];
    }
}

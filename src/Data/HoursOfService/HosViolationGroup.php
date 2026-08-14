<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * HOS violations grouped in one endpoint response item.
 *
 * @property-read array<int, array<string, mixed>>|null $violations
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HosViolationGroup extends Entity
{
    /**
     * Get the typed violations in this group.
     *
     * @return EntityCollection<int, HosViolation>
     */
    public function getViolations(): EntityCollection
    {
        $violations = $this->get('violations', []);

        if (! is_array($violations)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $violation): HosViolation => HosViolation::make($violation),
            array_values(array_filter($violations, 'is_array'))
        ));
    }
}

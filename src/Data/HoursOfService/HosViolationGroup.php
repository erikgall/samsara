<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * HOS violations grouped in one endpoint response item.
 *
 * @property-read array<int, array<string, mixed>>|null $violations
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
        return new EntityCollection(array_map(
            fn (array $violation): HosViolation => HosViolation::make($violation),
            $this->get('violations', [])
        ));
    }
}

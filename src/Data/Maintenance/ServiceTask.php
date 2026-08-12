<?php

namespace Samsara\Data\Maintenance;

use Samsara\Data\Entity;

/**
 * ServiceTask entity.
 *
 * Represents a reusable maintenance service task.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Service task ID
 * @property-read string|null $name Service task name
 * @property-read string|null $description Task description
 * @property-read string|null $category Task category
 * @property-read string|null $subcategory Task subcategory
 * @property-read int|null $estimatedLaborTimeMinutes Estimated labor time in minutes
 * @property-read array<string, mixed>|null $estimatedPartsCost Estimated parts cost
 */
class ServiceTask extends Entity
{
    /**
     * Get the estimated labor time in hours.
     */
    public function getEstimatedLaborHours(): ?float
    {
        $minutes = $this->get('estimatedLaborTimeMinutes');

        return is_int($minutes) ? $minutes / 60 : null;
    }
}

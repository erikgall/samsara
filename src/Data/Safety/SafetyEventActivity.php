<?php

namespace Samsara\Data\Safety;

use Samsara\Data\Entity;

/**
 * Safety activity feed item with its nested safety event.
 *
 * @property-read string|null $id
 * @property-read string|null $time
 * @property-read string|null $type
 * @property-read array<string, mixed>|null $safetyEvent
 */
class SafetyEventActivity extends Entity
{
    /**
     * Get the typed nested safety event.
     */
    public function getSafetyEvent(): SafetyEvent
    {
        return SafetyEvent::make($this->get('safetyEvent', []));
    }
}

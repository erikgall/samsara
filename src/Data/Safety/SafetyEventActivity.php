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

    /**
     * Check if this item records a behavior-label change.
     */
    public function isBehaviorLabelActivity(): bool
    {
        return $this->type === 'BehaviorLabelActivityType';
    }

    /**
     * Check if this item records a coaching-state change.
     */
    public function isCoachingStateActivity(): bool
    {
        return $this->type === 'CoachingStateActivityType';
    }

    /**
     * Check if this item records safety-event creation.
     */
    public function isCreateSafetyEventActivity(): bool
    {
        return $this->type === 'CreateSafetyEventActivityType';
    }
}

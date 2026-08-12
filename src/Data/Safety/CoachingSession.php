<?php

namespace Samsara\Data\Safety;

use Samsara\Data\Entity;

/**
 * CoachingSession entity.
 *
 * Represents a Samsara coaching session.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Coaching session ID
 * @property-read string|null $sessionStatus Session status (unknown, upcoming, completed, deleted)
 * @property-read string|null $coachingType Coaching type (selfCoaching, withManager, etc.)
 * @property-read string|null $dueAtTime Due date time (RFC 3339)
 * @property-read string|null $completedAtTime Completion time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 * @property-read string|null $assignedCoachId Assigned coach user ID
 * @property-read string|null $completedCoachId Completed coach user ID
 * @property-read string|null $sessionNote Session note
 * @property-read array{id?: string, name?: string}|null $driver Driver information
 * @property-read array<int, array{coachableBehaviorType: string}>|null $behaviors Behavior references
 */
class CoachingSession extends Entity
{
    /**
     * Check whether the session contains a coachable behavior type.
     */
    public function hasCoachableBehavior(string $behaviorType): bool
    {
        $behaviors = $this->get('behaviors');

        if (! is_array($behaviors)) {
            return false;
        }

        foreach ($behaviors as $behavior) {
            if (is_array($behavior) && ($behavior['coachableBehaviorType'] ?? null) === $behaviorType) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the session is completed.
     */
    public function isCompleted(): bool
    {
        return $this->sessionStatus === 'completed';
    }

    /**
     * Check if the session is deleted.
     */
    public function isDeleted(): bool
    {
        return $this->sessionStatus === 'deleted';
    }

    /**
     * Check if this is a self-coaching session.
     */
    public function isSelfCoaching(): bool
    {
        return $this->coachingType === 'selfCoaching';
    }

    /**
     * Check if the session is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->sessionStatus === 'upcoming';
    }

    /**
     * Check if this is a session with a manager.
     */
    public function isWithManager(): bool
    {
        return $this->coachingType === 'withManager';
    }
}

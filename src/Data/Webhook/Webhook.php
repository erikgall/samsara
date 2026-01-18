<?php

namespace Samsara\Data\Webhook;

use Samsara\Data\Entity;

/**
 * Webhook entity.
 *
 * Represents a webhook configuration.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Webhook ID
 * @property-read string|null $name Webhook name
 * @property-read string|null $url Webhook URL
 * @property-read string|null $secret Webhook secret
 * @property-read string|null $status Webhook status (active, paused)
 * @property-read array<int, string>|null $eventTypes Event types
 * @property-read array<int, array{name?: string, value?: string}>|null $customHeaders Custom headers
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 */
class Webhook extends Entity
{
    /**
     * Check if the webhook has a specific event type.
     */
    public function hasEventType(string $eventType): bool
    {
        $eventTypes = $this->eventTypes;

        if (! is_array($eventTypes)) {
            return false;
        }

        return in_array($eventType, $eventTypes, true);
    }

    /**
     * Check if the webhook is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the webhook is paused.
     */
    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }
}

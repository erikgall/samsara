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
 * @property-read string|null $secretKey Webhook secret
 * @property-read string|null $version Webhook API version
 * @property-read array<int, string>|null $eventTypes Event types
 * @property-read array<int, array{key: string, value: string}>|null $customHeaders Custom headers
 */
class Webhook extends Entity
{
    /**
     * Get the first custom header value for an exact key.
     */
    public function getCustomHeader(string $key): ?string
    {
        $headers = $this->get('customHeaders');

        if (! is_array($headers)) {
            return null;
        }

        foreach ($headers as $header) {
            if (! is_array($header) || ($header['key'] ?? null) !== $key) {
                continue;
            }

            $value = $header['value'] ?? null;

            return is_string($value) ? $value : null;
        }

        return null;
    }

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
}

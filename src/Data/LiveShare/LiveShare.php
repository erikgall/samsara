<?php

namespace Samsara\Data\LiveShare;

use Samsara\Data\Entity;

/**
 * LiveShare entity.
 *
 * Represents a live sharing link.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Live share ID
 * @property-read string|null $name Live share name
 * @property-read string|null $url Live share URL
 * @property-read string|null $status Live share status (active, expired)
 * @property-read string|null $expiresAt Expiration time (RFC 3339)
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read array<int, array{id?: string, type?: string}>|null $assets Shared assets
 * @property-read array<int, array{email?: string}>|null $recipients Share recipients
 */
class LiveShare extends Entity
{
    /**
     * Check if the live share is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the live share is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }
}

<?php

namespace Samsara\Data\LiveShare;

use DateTimeImmutable;
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
 * @property-read string|null $liveSharingUrl Live share URL
 * @property-read string|null $type Live share type
 * @property-read string|null $description Live share description
 * @property-read string|null $expiresAtTime Expiration time (RFC 3339)
 * @property-read array<string, mixed>|null $assetsLocationLinkConfig Assets-location configuration
 * @property-read array<string, mixed>|null $assetsNearLocationLinkConfig Assets-near-location configuration
 * @property-read array<string, mixed>|null $assetsOnRouteLinkConfig Assets-on-route configuration
 */
class LiveShare extends Entity
{
    /**
     * Check if the live share is active.
     */
    public function isActive(): bool
    {
        return ! $this->isExpired();
    }

    /**
     * Check if the live share is expired.
     */
    public function isExpired(): bool
    {
        if ($this->expiresAtTime === null) {
            return false;
        }

        return new DateTimeImmutable($this->expiresAtTime) <= new DateTimeImmutable;
    }
}

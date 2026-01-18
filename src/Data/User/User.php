<?php

namespace Samsara\Data\User;

use Samsara\Data\Entity;

/**
 * User entity.
 *
 * Represents a Samsara user.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id User ID
 * @property-read string|null $name User name
 * @property-read string|null $email User email
 * @property-read string|null $authType Authentication type (admin, driver, etc.)
 * @property-read array<int, array{id?: string, name?: string}>|null $roles User roles
 * @property-read array<int, string>|null $tagIds Tag IDs
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 */
class User extends Entity
{
    /**
     * Get the display name for the user.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? $this->email ?? '';
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->authType === 'admin';
    }

    /**
     * Check if the user is a driver.
     */
    public function isDriver(): bool
    {
        return $this->authType === 'driver';
    }
}

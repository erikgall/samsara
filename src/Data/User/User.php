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
 * @property-read string|null $authType Authentication type (default or saml)
 * @property-read array<int, array{id?: string, name?: string}>|null $roles User roles
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
}

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
 * @property-read array<int, array{role?: array{id?: string, name?: string}, tag?: array{id?: string, name?: string}, expireAt?: string}>|null $roles User role assignments
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
     * Get the role IDs from the user's role assignments.
     *
     * @return array<int, string>
     */
    public function getRoleIds(): array
    {
        $roles = $this->get('roles', []);

        if (! is_array($roles)) {
            return [];
        }

        $roleIds = [];

        foreach ($roles as $assignment) {
            if (! is_array($assignment)) {
                continue;
            }

            $role = $assignment['role'] ?? null;

            if (! is_array($role)) {
                continue;
            }

            $roleId = $role['id'] ?? null;

            if (is_string($roleId)) {
                $roleIds[] = $roleId;
            }
        }

        return $roleIds;
    }
}

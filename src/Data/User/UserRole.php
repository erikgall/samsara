<?php

namespace ErikGall\Samsara\Data\User;

use ErikGall\Samsara\Data\Entity;

/**
 * UserRole entity.
 *
 * Represents a user role.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Role ID
 * @property-read string|null $name Role name
 * @property-read string|null $description Role description
 * @property-read array<int, array{name?: string, resource?: string}>|null $permissions Role permissions
 */
class UserRole extends Entity {}

<?php

namespace Samsara\Tests\Unit\Data\User;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\User\UserRole;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the UserRole entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class UserRoleTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $userRole = new UserRole([
            'id'          => '12345',
            'name'        => 'Fleet Manager',
            'description' => 'Manages fleet operations',
        ]);

        $this->assertSame('12345', $userRole->id);
        $this->assertSame('Fleet Manager', $userRole->name);
        $this->assertSame('Manages fleet operations', $userRole->description);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $userRole = UserRole::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(UserRole::class, $userRole);
        $this->assertSame('12345', $userRole->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Fleet Manager',
        ];

        $userRole = new UserRole($data);

        $this->assertSame($data, $userRole->toArray());
    }

    #[Test]
    public function it_can_have_permissions(): void
    {
        $userRole = new UserRole([
            'permissions' => [
                ['name' => 'read', 'resource' => 'vehicles'],
                ['name' => 'write', 'resource' => 'drivers'],
            ],
        ]);

        $this->assertCount(2, $userRole->permissions);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $userRole = new UserRole;

        $this->assertInstanceOf(Entity::class, $userRole);
    }
}

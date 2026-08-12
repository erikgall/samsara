<?php

namespace Samsara\Tests\Unit\Data\User;

use Samsara\Data\Entity;
use Samsara\Data\User\User;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the User entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class UserTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $user = new User([
            'id'    => '12345',
            'name'  => 'John Smith',
            'email' => 'john@example.com',
        ]);

        $this->assertSame('12345', $user->id);
        $this->assertSame('John Smith', $user->name);
        $this->assertSame('john@example.com', $user->email);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $user = User::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('12345', $user->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'    => '12345',
            'name'  => 'John Smith',
            'email' => 'john@example.com',
        ];

        $user = new User($data);

        $this->assertSame($data, $user->toArray());
    }

    #[Test]
    public function it_can_get_display_name(): void
    {
        $user = new User([
            'name' => 'John Smith',
        ]);

        $this->assertSame('John Smith', $user->getDisplayName());
    }

    #[Test]
    public function it_can_have_roles(): void
    {
        $user = new User([
            'roles' => [
                ['role' => ['id' => 'role-1', 'name' => 'Fleet Manager']],
                ['role' => ['id' => 'role-2', 'name' => 'Safety Admin']],
            ],
        ]);

        $this->assertCount(2, $user->roles);
        $this->assertSame('Fleet Manager', $user->roles[0]['role']['name']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $user = new User;

        $this->assertInstanceOf(Entity::class, $user);
    }

    #[Test]
    public function it_gets_role_ids_from_current_role_assignments(): void
    {
        $user = new User([
            'roles' => [
                ['role' => ['id' => 'role-1']],
                ['role' => ['id' => 'role-2']],
                ['role' => ['id' => 'role-1']],
                ['role' => ['id' => '']],
                ['role' => ['id' => 123]],
                ['role' => []],
                ['role' => null],
                [],
                'malformed-assignment',
            ],
        ]);

        $this->assertSame(['role-1', 'role-2', 'role-1', ''], $user->getRoleIds());
    }

    #[Test]
    public function it_returns_email_for_display_name_when_name_not_set(): void
    {
        $user = new User([
            'email' => 'john@example.com',
        ]);

        $this->assertSame('john@example.com', $user->getDisplayName());
    }

    #[Test]
    public function it_returns_no_role_ids_for_missing_or_malformed_roles(): void
    {
        $users = [
            new User,
            new User(['roles' => null]),
            new User(['roles' => 'role-1']),
        ];

        foreach ($users as $user) {
            $this->assertSame([], $user->getRoleIds());
        }
    }

    #[Test]
    public function it_uses_current_auth_types_without_role_helpers(): void
    {
        $user = new User([
            'authType' => 'saml',
        ]);

        $this->assertSame('saml', $user->authType);
        $this->assertFalse(method_exists($user, 'isAdmin'));
        $this->assertFalse(method_exists($user, 'isDriver'));
    }
}

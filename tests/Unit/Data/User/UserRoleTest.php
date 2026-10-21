<?php

namespace Samsara\Tests\Unit\Data\User;

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
    public function it_can_have_a_name(): void
    {
        $userRole = new UserRole([
            'name' => 'Full Admin',
        ]);

        $this->assertSame('Full Admin', $userRole->name);
    }
}

<?php

namespace Samsara\Tests\Unit\Contracts;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Contracts\EntityInterface;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Unit tests for the EntityInterface contract.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EntityInterfaceTest extends TestCase
{
    #[Test]
    public function it_defines_get_id_method(): void
    {
        $methods = (new \ReflectionClass(EntityInterface::class))->getMethods();

        $methodNames = array_map(fn ($m) => $m->getName(), $methods);

        $this->assertContains('getId', $methodNames);
    }

    #[Test]
    public function it_extends_arrayable(): void
    {
        $reflection = new \ReflectionClass(EntityInterface::class);

        $this->assertTrue($reflection->implementsInterface(Arrayable::class));
    }

    #[Test]
    public function it_extends_jsonable(): void
    {
        $reflection = new \ReflectionClass(EntityInterface::class);

        $this->assertTrue($reflection->implementsInterface(Jsonable::class));
    }

    #[Test]
    public function it_is_an_interface(): void
    {
        $reflection = new \ReflectionClass(EntityInterface::class);

        $this->assertTrue($reflection->isInterface());
    }
}

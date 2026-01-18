<?php

namespace Samsara\Tests\Unit\Data;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Illuminate\Support\Fluent;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Contracts\EntityInterface;

/**
 * Unit tests for the Entity base class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EntityTest extends TestCase
{
    #[Test]
    public function it_can_access_attributes_as_array(): void
    {
        $entity = new Entity(['name' => 'Test']);

        $this->assertSame('Test', $entity['name']);
    }

    #[Test]
    public function it_can_access_attributes_as_properties(): void
    {
        $entity = new Entity(['name' => 'Test']);

        $this->assertSame('Test', $entity->name);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $entity = Entity::make(['id' => '123', 'name' => 'Test']);

        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertSame('123', $entity->id);
        $this->assertSame('Test', $entity->name);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $entity = new Entity(['id' => '123', 'name' => 'Test']);

        $array = $entity->toArray();

        $this->assertSame(['id' => '123', 'name' => 'Test'], $array);
    }

    #[Test]
    public function it_can_convert_to_json(): void
    {
        $entity = new Entity(['id' => '123', 'name' => 'Test']);

        $json = $entity->toJson();

        $this->assertSame('{"id":"123","name":"Test"}', $json);
    }

    #[Test]
    public function it_can_fill_attributes(): void
    {
        $entity = new Entity;
        $result = $entity->fill(['name' => 'Test', 'value' => 42]);

        $this->assertSame($entity, $result);
        $this->assertSame('Test', $entity->name);
        $this->assertSame(42, $entity->value);
    }

    #[Test]
    public function it_can_get_id(): void
    {
        $entity = new Entity(['id' => 'abc-123']);

        $this->assertSame('abc-123', $entity->getId());
    }

    #[Test]
    public function it_extends_fluent(): void
    {
        $entity = new Entity;

        $this->assertInstanceOf(Fluent::class, $entity);
    }

    #[Test]
    public function it_implements_entity_interface(): void
    {
        $entity = new Entity;

        $this->assertInstanceOf(EntityInterface::class, $entity);
    }

    #[Test]
    public function it_is_json_serializable(): void
    {
        $entity = new Entity(['id' => '123']);

        $json = json_encode($entity);

        $this->assertSame('{"id":"123"}', $json);
    }

    #[Test]
    public function it_returns_null_when_no_id(): void
    {
        $entity = new Entity;

        $this->assertNull($entity->getId());
    }
}

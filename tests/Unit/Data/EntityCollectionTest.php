<?php

namespace Samsara\Tests\Unit\Data;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Illuminate\Support\Collection;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the EntityCollection class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EntityCollectionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_array_of_entities(): void
    {
        $entities = [
            Entity::make(['id' => '1']),
            Entity::make(['id' => '2']),
        ];

        $collection = new EntityCollection($entities);

        $this->assertCount(2, $collection);
    }

    #[Test]
    public function it_can_find_entity_by_id(): void
    {
        $entity1 = Entity::make(['id' => 'abc-123', 'name' => 'First']);
        $entity2 = Entity::make(['id' => 'def-456', 'name' => 'Second']);

        $collection = new EntityCollection([$entity1, $entity2]);

        $found = $collection->findById('def-456');

        $this->assertSame($entity2, $found);
    }

    #[Test]
    public function it_can_get_all_ids(): void
    {
        $entity1 = Entity::make(['id' => 'abc-123']);
        $entity2 = Entity::make(['id' => 'def-456']);
        $entity3 = Entity::make(['name' => 'No ID']);

        $collection = new EntityCollection([$entity1, $entity2, $entity3]);

        $ids = $collection->ids();

        $this->assertSame(['abc-123', 'def-456'], $ids);
    }

    #[Test]
    public function it_extends_collection(): void
    {
        $collection = new EntityCollection;

        $this->assertInstanceOf(Collection::class, $collection);
    }

    #[Test]
    public function it_returns_empty_array_for_empty_collection(): void
    {
        $collection = new EntityCollection;

        $this->assertSame([], $collection->ids());
    }

    #[Test]
    public function it_returns_null_when_entity_not_found_by_id(): void
    {
        $entity = Entity::make(['id' => 'abc-123']);

        $collection = new EntityCollection([$entity]);

        $this->assertNull($collection->findById('not-found'));
    }
}

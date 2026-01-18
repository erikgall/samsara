<?php

namespace Samsara\Tests\Unit\Data\Equipment;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Equipment\Equipment;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Equipment entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $equipment = new Equipment([
            'id'          => '112',
            'name'        => 'Generator A1',
            'assetSerial' => '1FUJA6BD31LJ09646',
        ]);

        $this->assertSame('112', $equipment->id);
        $this->assertSame('Generator A1', $equipment->name);
        $this->assertSame('1FUJA6BD31LJ09646', $equipment->assetSerial);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $equipment = Equipment::make([
            'id'   => '112',
            'name' => 'Generator A1',
        ]);

        $this->assertInstanceOf(Equipment::class, $equipment);
        $this->assertSame('112', $equipment->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '112',
            'name' => 'Generator A1',
        ];

        $equipment = new Equipment($data);

        $this->assertSame($data, $equipment->toArray());
    }

    #[Test]
    public function it_can_get_display_name(): void
    {
        $equipment = new Equipment([
            'name' => 'Generator A1',
        ]);

        $this->assertSame('Generator A1', $equipment->getDisplayName());
    }

    #[Test]
    public function it_can_get_external_id(): void
    {
        $equipment = new Equipment([
            'externalIds' => [
                'assetId' => 'A12345',
            ],
        ]);

        $this->assertSame('A12345', $equipment->getExternalId('assetId'));
    }

    #[Test]
    public function it_can_get_tag_ids(): void
    {
        $equipment = new Equipment([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
                ['id' => '4815', 'name' => 'West Coast'],
            ],
        ]);

        $this->assertSame(['3914', '4815'], $equipment->getTagIds());
    }

    #[Test]
    public function it_can_have_external_ids(): void
    {
        $equipment = new Equipment([
            'externalIds' => [
                'assetId' => 'A12345',
            ],
        ]);

        $this->assertSame('A12345', $equipment->externalIds['assetId']);
    }

    #[Test]
    public function it_can_have_installed_gateway(): void
    {
        $equipment = new Equipment([
            'installedGateway' => [
                'serial' => 'ABCD-123-XYZ',
                'model'  => 'AG24',
            ],
        ]);

        $this->assertIsArray($equipment->installedGateway);
        $this->assertSame('ABCD-123-XYZ', $equipment->installedGateway['serial']);
    }

    #[Test]
    public function it_can_have_notes(): void
    {
        $equipment = new Equipment([
            'notes' => 'These are notes about this equipment.',
        ]);

        $this->assertSame('These are notes about this equipment.', $equipment->notes);
    }

    #[Test]
    public function it_can_have_tags(): void
    {
        $equipment = new Equipment([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
            ],
        ]);

        $this->assertCount(1, $equipment->tags);
        $this->assertSame('East Coast', $equipment->tags[0]['name']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $equipment = new Equipment;

        $this->assertInstanceOf(Entity::class, $equipment);
    }

    #[Test]
    public function it_returns_empty_array_when_no_tags(): void
    {
        $equipment = new Equipment;

        $this->assertSame([], $equipment->getTagIds());
    }

    #[Test]
    public function it_returns_null_for_missing_external_id(): void
    {
        $equipment = new Equipment([
            'externalIds' => [],
        ]);

        $this->assertNull($equipment->getExternalId('assetId'));
    }

    #[Test]
    public function it_returns_unknown_for_missing_display_name(): void
    {
        $equipment = new Equipment;

        $this->assertSame('Unknown', $equipment->getDisplayName());
    }
}

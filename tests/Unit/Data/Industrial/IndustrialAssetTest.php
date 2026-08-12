<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\IndustrialAsset;

/**
 * Unit tests for the IndustrialAsset entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IndustrialAssetTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $asset = new IndustrialAsset([
            'id'   => '12345',
            'name' => 'Generator A',
        ]);

        $this->assertSame('12345', $asset->id);
        $this->assertSame('Generator A', $asset->name);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $asset = IndustrialAsset::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(IndustrialAsset::class, $asset);
        $this->assertSame('12345', $asset->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Generator A',
        ];

        $asset = new IndustrialAsset($data);

        $this->assertSame($data, $asset->toArray());
    }

    #[Test]
    public function it_can_have_data_outputs(): void
    {
        $asset = new IndustrialAsset([
            'dataOutputs' => [
                ['id' => 'output-1', 'name' => 'Pump Control'],
            ],
        ]);

        $this->assertCount(1, $asset->dataOutputs);
    }

    #[Test]
    public function it_can_have_location(): void
    {
        $asset = new IndustrialAsset([
            'location' => [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $this->assertSame(37.7749, $asset->location['latitude']);
    }

    #[Test]
    public function it_can_have_tags(): void
    {
        $asset = new IndustrialAsset([
            'tags' => [
                ['id' => 'tag-1', 'name' => 'Power Equipment'],
            ],
        ]);

        $this->assertCount(1, $asset->tags);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $asset = new IndustrialAsset;

        $this->assertInstanceOf(Entity::class, $asset);
    }

    #[Test]
    public function it_gets_coordinates_with_partial_data(): void
    {
        $asset = new IndustrialAsset([
            'location' => ['latitude' => 37.7749],
        ]);

        $this->assertSame([
            'latitude'  => 37.7749,
            'longitude' => null,
        ], $asset->getCoordinates());
        $this->assertSame([
            'latitude'  => null,
            'longitude' => null,
        ], (new IndustrialAsset)->getCoordinates());
    }

    #[Test]
    public function it_gets_valid_tag_ids_in_payload_order(): void
    {
        $asset = new IndustrialAsset([
            'tags' => [
                ['id' => 'tag-1'],
                ['name' => 'Missing ID'],
                ['id'   => 'tag-2'],
            ],
        ]);

        $this->assertSame(['tag-1', 'tag-2'], $asset->getTagIds());
        $this->assertSame([], (new IndustrialAsset)->getTagIds());
    }

    #[Test]
    public function it_only_reports_an_exact_running_boolean(): void
    {
        $this->assertTrue((new IndustrialAsset(['isRunning' => true]))->isRunning());

        foreach ([new IndustrialAsset, new IndustrialAsset(['isRunning' => null]), new IndustrialAsset(['isRunning' => false]), new IndustrialAsset(['isRunning' => 1])] as $asset) {
            $this->assertFalse($asset->isRunning());
        }
    }
}

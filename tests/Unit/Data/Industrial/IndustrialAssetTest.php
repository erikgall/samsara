<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Industrial;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Industrial\IndustrialAsset;

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
    public function it_can_have_data_inputs(): void
    {
        $asset = new IndustrialAsset([
            'dataInputs' => [
                ['id' => 'input-1', 'name' => 'Temperature'],
            ],
        ]);

        $this->assertCount(1, $asset->dataInputs);
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
}

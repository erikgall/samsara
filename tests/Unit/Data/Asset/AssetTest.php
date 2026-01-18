<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Asset;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use ErikGall\Samsara\Data\Asset\Asset;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Asset entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AssetTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $asset = new Asset([
            'id'            => '12345',
            'name'          => 'Forklift A',
            'serial'        => 'SN12345',
            'purchasePrice' => 25000.00,
            'assetType'     => 'equipment',
        ]);

        $this->assertSame('12345', $asset->id);
        $this->assertSame('Forklift A', $asset->name);
        $this->assertSame('SN12345', $asset->serial);
        $this->assertSame(25000.00, $asset->purchasePrice);
        $this->assertSame('equipment', $asset->assetType);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $asset = Asset::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame('12345', $asset->getId());
    }

    #[Test]
    public function it_can_check_if_equipment(): void
    {
        $asset = new Asset([
            'assetType' => 'equipment',
        ]);

        $this->assertTrue($asset->isEquipment());
        $this->assertFalse($asset->isTrailer());
    }

    #[Test]
    public function it_can_check_if_trailer(): void
    {
        $asset = new Asset([
            'assetType' => 'trailer',
        ]);

        $this->assertTrue($asset->isTrailer());
        $this->assertFalse($asset->isEquipment());
    }

    #[Test]
    public function it_can_check_if_vehicle(): void
    {
        $asset = new Asset([
            'assetType' => 'vehicle',
        ]);

        $this->assertTrue($asset->isVehicle());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Forklift A',
        ];

        $asset = new Asset($data);

        $this->assertSame($data, $asset->toArray());
    }

    #[Test]
    public function it_can_have_gateway(): void
    {
        $asset = new Asset([
            'gateway' => [
                'id'     => 'gw-1',
                'serial' => 'GW12345',
            ],
        ]);

        $this->assertSame('gw-1', $asset->gateway['id']);
    }

    #[Test]
    public function it_can_have_location(): void
    {
        $asset = new Asset([
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
        $asset = new Asset([
            'tags' => [
                ['id' => 'tag-1', 'name' => 'Heavy Equipment'],
            ],
        ]);

        $this->assertCount(1, $asset->tags);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $asset = new Asset([
            'createdAtTime' => '2024-04-10T07:06:25Z',
            'updatedAtTime' => '2024-04-11T10:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $asset->createdAtTime);
        $this->assertSame('2024-04-11T10:00:00Z', $asset->updatedAtTime);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $asset = new Asset;

        $this->assertInstanceOf(Entity::class, $asset);
    }
}

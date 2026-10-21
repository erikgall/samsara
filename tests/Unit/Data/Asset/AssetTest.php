<?php

namespace Samsara\Tests\Unit\Data\Asset;

use Samsara\Tests\TestCase;
use Samsara\Data\Asset\Asset;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Asset entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AssetTest extends TestCase
{
    #[Test]
    public function it_can_check_if_equipment(): void
    {
        $asset = new Asset([
            'type' => 'equipment',
        ]);

        $this->assertTrue($asset->isEquipment());
        $this->assertFalse($asset->isTrailer());
    }

    #[Test]
    public function it_can_check_if_trailer(): void
    {
        $asset = new Asset([
            'type' => 'trailer',
        ]);

        $this->assertTrue($asset->isTrailer());
        $this->assertFalse($asset->isEquipment());
    }

    #[Test]
    public function it_can_check_if_vehicle(): void
    {
        $asset = new Asset([
            'type' => 'vehicle',
        ]);

        $this->assertTrue($asset->isVehicle());
    }

    #[Test]
    public function it_can_check_the_remaining_documented_asset_types(): void
    {
        $this->assertTrue((new Asset(['type' => 'unpowered']))->isUnpowered());
        $this->assertTrue((new Asset(['type' => 'uncategorized']))->isUncategorized());
        $this->assertFalse((new Asset(['type' => null]))->isUnpowered());
        $this->assertFalse((new Asset(['type' => 'equipment']))->isUncategorized());
    }

    #[Test]
    public function it_can_get_an_external_id_and_tag_ids(): void
    {
        $asset = new Asset([
            'externalIds' => ['maintenanceId' => 'asset-123'],
            'tags'        => [
                ['id' => 'tag-1', 'name' => 'Heavy Equipment'],
                ['id' => 2],
            ],
        ]);

        $this->assertSame('asset-123', $asset->getExternalId('maintenanceId'));
        $this->assertNull($asset->getExternalId('missing'));
        $this->assertSame(['tag-1', '2'], $asset->getTagIds());
        $this->assertSame([], (new Asset(['tags' => null]))->getTagIds());
    }

    #[Test]
    public function it_can_have_external_ids(): void
    {
        $asset = new Asset([
            'externalIds' => ['maintenanceId' => 'asset-123'],
        ]);

        $this->assertSame('asset-123', $asset->externalIds['maintenanceId']);
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
    public function it_can_have_vehicle_metadata(): void
    {
        $asset = new Asset([
            'make'         => 'Bobcat',
            'model'        => 'S630 T4',
            'licensePlate' => 'XHK1234',
            'year'         => 2015,
        ]);

        $this->assertSame('Bobcat', $asset->make);
        $this->assertSame('S630 T4', $asset->model);
        $this->assertSame(2015, $asset->year);
    }
}

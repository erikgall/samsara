<?php

namespace Samsara\Tests\Unit\Data\Industrial;

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

<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Trailer;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Trailer\Trailer;

/**
 * Unit tests for the Trailer entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TrailerTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $trailer = new Trailer([
            'id'                => '112',
            'name'              => 'Trailer A7',
            'assetSerialNumber' => 'ABC123',
            'licensePlate'      => 'XHK1234',
        ]);

        $this->assertSame('112', $trailer->id);
        $this->assertSame('Trailer A7', $trailer->name);
        $this->assertSame('ABC123', $trailer->assetSerialNumber);
        $this->assertSame('XHK1234', $trailer->licensePlate);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $trailer = Trailer::make([
            'id'   => '112',
            'name' => 'Trailer A7',
        ]);

        $this->assertInstanceOf(Trailer::class, $trailer);
        $this->assertSame('112', $trailer->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '112',
            'name' => 'Trailer A7',
        ];

        $trailer = new Trailer($data);

        $this->assertSame($data, $trailer->toArray());
    }

    #[Test]
    public function it_can_get_display_name(): void
    {
        $trailer = new Trailer([
            'name' => 'Trailer A7',
        ]);

        $this->assertSame('Trailer A7', $trailer->getDisplayName());
    }

    #[Test]
    public function it_can_get_external_id(): void
    {
        $trailer = new Trailer([
            'externalIds' => [
                'fleetId' => 'F12345',
            ],
        ]);

        $this->assertSame('F12345', $trailer->getExternalId('fleetId'));
    }

    #[Test]
    public function it_can_get_tag_ids(): void
    {
        $trailer = new Trailer([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
                ['id' => '4815', 'name' => 'West Coast'],
            ],
        ]);

        $this->assertSame(['3914', '4815'], $trailer->getTagIds());
    }

    #[Test]
    public function it_can_have_enabled_for_mobile_flag(): void
    {
        $trailer = new Trailer([
            'enabledForMobile' => true,
        ]);

        $this->assertTrue($trailer->enabledForMobile);
    }

    #[Test]
    public function it_can_have_external_ids(): void
    {
        $trailer = new Trailer([
            'externalIds' => [
                'fleetId' => 'F12345',
            ],
        ]);

        $this->assertSame('F12345', $trailer->externalIds['fleetId']);
    }

    #[Test]
    public function it_can_have_notes(): void
    {
        $trailer = new Trailer([
            'notes' => 'Refrigerated trailer',
        ]);

        $this->assertSame('Refrigerated trailer', $trailer->notes);
    }

    #[Test]
    public function it_can_have_tags(): void
    {
        $trailer = new Trailer([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
            ],
        ]);

        $this->assertCount(1, $trailer->tags);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $trailer = new Trailer;

        $this->assertInstanceOf(Entity::class, $trailer);
    }

    #[Test]
    public function it_returns_empty_array_when_no_tags(): void
    {
        $trailer = new Trailer;

        $this->assertSame([], $trailer->getTagIds());
    }

    #[Test]
    public function it_returns_null_for_missing_external_id(): void
    {
        $trailer = new Trailer([
            'externalIds' => [],
        ]);

        $this->assertNull($trailer->getExternalId('fleetId'));
    }

    #[Test]
    public function it_returns_unknown_for_missing_display_name(): void
    {
        $trailer = new Trailer;

        $this->assertSame('Unknown', $trailer->getDisplayName());
    }
}

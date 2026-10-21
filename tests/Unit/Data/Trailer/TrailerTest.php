<?php

namespace Samsara\Tests\Unit\Data\Trailer;

use Samsara\Tests\TestCase;
use Samsara\Data\Trailer\Trailer;
use Samsara\Data\Vehicle\Gateway;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Trailer entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TrailerTest extends TestCase
{
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
    public function it_can_get_the_installed_gateway_as_an_entity(): void
    {
        $trailer = new Trailer([
            'installedGateway' => ['serial' => 'AG-123', 'model' => 'AG52'],
        ]);

        $gateway = $trailer->getInstalledGateway();

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame('AG-123', $gateway->serial);

        foreach ([new Trailer, new Trailer(['installedGateway' => null]), new Trailer(['installedGateway' => []]), new Trailer(['installedGateway' => 'invalid'])] as $empty) {
            $this->assertNull($empty->getInstalledGateway());
        }
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
    public function it_can_have_item_response_attributes(): void
    {
        $trailer = new Trailer([
            'attributes' => [['id' => 'attribute-1', 'name' => 'Reefer', 'value' => 'yes']],
        ]);

        $this->assertCount(1, $trailer->attributes);
        $this->assertSame('Reefer', $trailer->attributes[0]['name']);
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

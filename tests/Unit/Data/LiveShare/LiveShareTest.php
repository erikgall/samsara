<?php

namespace Samsara\Tests\Unit\Data\LiveShare;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\LiveShare\LiveShare;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the LiveShare entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class LiveShareTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $liveShare = new LiveShare([
            'id'             => '12345',
            'name'           => 'Fleet Live View',
            'liveSharingUrl' => 'https://cloud.samsara.com/live/abc123',
            'expiresAtTime'  => '2999-04-15T00:00:00Z',
            'type'           => 'assetsLocation',
        ]);

        $this->assertSame('12345', $liveShare->id);
        $this->assertSame('Fleet Live View', $liveShare->name);
        $this->assertSame('https://cloud.samsara.com/live/abc123', $liveShare->liveSharingUrl);
        $this->assertSame('2999-04-15T00:00:00Z', $liveShare->expiresAtTime);
        $this->assertSame('assetsLocation', $liveShare->type);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $liveShare = LiveShare::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(LiveShare::class, $liveShare);
        $this->assertSame('12345', $liveShare->getId());
    }

    #[Test]
    public function it_can_check_if_active(): void
    {
        $liveShare = new LiveShare(['expiresAtTime' => '2999-01-01T00:00:00Z']);

        $this->assertTrue($liveShare->isActive());
        $this->assertFalse($liveShare->isExpired());
    }

    #[Test]
    public function it_can_check_if_expired(): void
    {
        $liveShare = new LiveShare(['expiresAtTime' => '2000-01-01T00:00:00Z']);

        $this->assertTrue($liveShare->isExpired());
        $this->assertFalse($liveShare->isActive());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'             => '12345',
            'name'           => 'Fleet Live View',
            'liveSharingUrl' => 'https://cloud.samsara.com/live/abc123',
        ];

        $liveShare = new LiveShare($data);

        $this->assertSame($data, $liveShare->toArray());
    }

    #[Test]
    public function it_can_have_type_specific_configuration(): void
    {
        $liveShare = new LiveShare([
            'assetsLocationLinkConfig' => [
                'assetIds' => ['vehicle-1', 'vehicle-2'],
            ],
        ]);

        $this->assertSame(['vehicle-1', 'vehicle-2'], $liveShare->assetsLocationLinkConfig['assetIds']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $liveShare = new LiveShare;

        $this->assertInstanceOf(Entity::class, $liveShare);
    }
}

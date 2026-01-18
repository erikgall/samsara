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
            'id'        => '12345',
            'name'      => 'Fleet Live View',
            'url'       => 'https://cloud.samsara.com/live/abc123',
            'expiresAt' => '2024-04-15T00:00:00Z',
        ]);

        $this->assertSame('12345', $liveShare->id);
        $this->assertSame('Fleet Live View', $liveShare->name);
        $this->assertSame('https://cloud.samsara.com/live/abc123', $liveShare->url);
        $this->assertSame('2024-04-15T00:00:00Z', $liveShare->expiresAt);
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
        $liveShare = new LiveShare([
            'status' => 'active',
        ]);

        $this->assertTrue($liveShare->isActive());
        $this->assertFalse($liveShare->isExpired());
    }

    #[Test]
    public function it_can_check_if_expired(): void
    {
        $liveShare = new LiveShare([
            'status' => 'expired',
        ]);

        $this->assertTrue($liveShare->isExpired());
        $this->assertFalse($liveShare->isActive());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Fleet Live View',
            'url'  => 'https://cloud.samsara.com/live/abc123',
        ];

        $liveShare = new LiveShare($data);

        $this->assertSame($data, $liveShare->toArray());
    }

    #[Test]
    public function it_can_have_assets(): void
    {
        $liveShare = new LiveShare([
            'assets' => [
                ['id' => 'asset-1', 'type' => 'vehicle'],
                ['id' => 'asset-2', 'type' => 'driver'],
            ],
        ]);

        $this->assertCount(2, $liveShare->assets);
    }

    #[Test]
    public function it_can_have_recipients(): void
    {
        $liveShare = new LiveShare([
            'recipients' => [
                ['email' => 'user@example.com'],
            ],
        ]);

        $this->assertCount(1, $liveShare->recipients);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $liveShare = new LiveShare([
            'createdAtTime' => '2024-04-10T07:06:25Z',
            'expiresAt'     => '2024-04-15T00:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $liveShare->createdAtTime);
        $this->assertSame('2024-04-15T00:00:00Z', $liveShare->expiresAt);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $liveShare = new LiveShare;

        $this->assertInstanceOf(Entity::class, $liveShare);
    }
}

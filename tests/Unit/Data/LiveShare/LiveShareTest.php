<?php

namespace Samsara\Tests\Unit\Data\LiveShare;

use Samsara\Tests\TestCase;
use Illuminate\Support\Carbon;
use Samsara\Data\LiveShare\LiveShare;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the LiveShare entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class LiveShareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2024-01-15T10:30:00Z');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
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
    public function it_checks_each_documented_link_type(): void
    {
        $typeMethods = [
            'assetsLocation'     => 'isAssetsLocationType',
            'assetsNearLocation' => 'isAssetsNearLocationType',
            'assetsOnRoute'      => 'isAssetsOnRouteType',
        ];

        foreach ($typeMethods as $type => $expectedMethod) {
            $liveShare = new LiveShare(['type' => $type]);

            foreach ($typeMethods as $method) {
                $this->assertSame($method === $expectedMethod, $liveShare->{$method}());
            }
        }

        foreach ([new LiveShare, new LiveShare(['type' => null]), new LiveShare(['type' => 'unknown'])] as $liveShare) {
            foreach ($typeMethods as $method) {
                $this->assertFalse($liveShare->{$method}());
            }
        }
    }

    #[Test]
    public function it_checks_expiration_at_the_current_instant(): void
    {
        $this->assertTrue((new LiveShare(['expiresAtTime' => '2024-01-15T10:29:59Z']))->isExpired());
        $this->assertTrue((new LiveShare(['expiresAtTime' => '2024-01-15T10:30:00Z']))->isExpired());
        $this->assertTrue((new LiveShare(['expiresAtTime' => '2024-01-15T05:30:00-05:00']))->isExpired());
        $this->assertFalse((new LiveShare(['expiresAtTime' => '2024-01-15T10:30:00.001Z']))->isExpired());
        $this->assertFalse((new LiveShare)->isExpired());
        $this->assertFalse((new LiveShare(['expiresAtTime' => null]))->isExpired());
    }
}

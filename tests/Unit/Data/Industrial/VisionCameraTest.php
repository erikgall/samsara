<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\VisionCamera;

/**
 * Unit tests for the VisionCamera entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VisionCameraTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $camera = new VisionCamera([
            'cameraId'   => 12345,
            'cameraName' => 'Line 32',
            'ethernetIp' => '10.1.2.34',
            'wifiIp'     => '10.1.2.35',
        ]);

        $this->assertSame(12345, $camera->cameraId);
        $this->assertSame('Line 32', $camera->cameraName);
        $this->assertSame('10.1.2.34', $camera->ethernetIp);
        $this->assertSame('10.1.2.35', $camera->wifiIp);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $camera = VisionCamera::make([
            'cameraId' => 12345,
        ]);

        $this->assertInstanceOf(VisionCamera::class, $camera);
        $this->assertNull($camera->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'cameraId'   => 12345,
            'cameraName' => 'Line 32',
        ];

        $camera = new VisionCamera($data);

        $this->assertSame($data, $camera->toArray());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $camera = new VisionCamera;

        $this->assertInstanceOf(Entity::class, $camera);
    }
}

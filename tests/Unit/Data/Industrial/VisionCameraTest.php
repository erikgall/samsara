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
            'id'           => '12345',
            'serialNo'     => 'CAM-001',
            'streamingUrl' => 'rtsp://camera.example.com/stream1',
        ]);

        $this->assertSame('12345', $camera->id);
        $this->assertSame('CAM-001', $camera->serialNo);
        $this->assertSame('rtsp://camera.example.com/stream1', $camera->streamingUrl);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $camera = VisionCamera::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(VisionCamera::class, $camera);
        $this->assertSame('12345', $camera->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'       => '12345',
            'serialNo' => 'CAM-001',
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

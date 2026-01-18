<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Maintenance;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Maintenance\Dvir;

/**
 * Unit tests for the Dvir entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DvirTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $dvir = new Dvir([
            'id'           => '7107471',
            'type'         => 'preTrip',
            'safetyStatus' => 'safe',
            'startTime'    => '2024-04-10T07:06:25Z',
            'endTime'      => '2024-04-10T07:30:00Z',
        ]);

        $this->assertSame('7107471', $dvir->id);
        $this->assertSame('preTrip', $dvir->type);
        $this->assertSame('safe', $dvir->safetyStatus);
        $this->assertSame('2024-04-10T07:06:25Z', $dvir->startTime);
        $this->assertSame('2024-04-10T07:30:00Z', $dvir->endTime);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $dvir = Dvir::make([
            'id' => '7107471',
        ]);

        $this->assertInstanceOf(Dvir::class, $dvir);
        $this->assertSame('7107471', $dvir->getId());
    }

    #[Test]
    public function it_can_check_if_mechanic_inspection(): void
    {
        $dvir = new Dvir([
            'type' => 'mechanic',
        ]);

        $this->assertTrue($dvir->isMechanicInspection());
    }

    #[Test]
    public function it_can_check_if_post_trip(): void
    {
        $dvir = new Dvir([
            'type' => 'postTrip',
        ]);

        $this->assertTrue($dvir->isPostTrip());
        $this->assertFalse($dvir->isPreTrip());
    }

    #[Test]
    public function it_can_check_if_pre_trip(): void
    {
        $dvir = new Dvir([
            'type' => 'preTrip',
        ]);

        $this->assertTrue($dvir->isPreTrip());
        $this->assertFalse($dvir->isPostTrip());
    }

    #[Test]
    public function it_can_check_if_resolved(): void
    {
        $dvir = new Dvir([
            'safetyStatus' => 'resolved',
        ]);

        $this->assertTrue($dvir->isResolved());
        $this->assertFalse($dvir->isSafe());
    }

    #[Test]
    public function it_can_check_if_safe(): void
    {
        $dvir = new Dvir([
            'safetyStatus' => 'safe',
        ]);

        $this->assertTrue($dvir->isSafe());
        $this->assertFalse($dvir->isUnsafe());
        $this->assertFalse($dvir->isResolved());
    }

    #[Test]
    public function it_can_check_if_unsafe(): void
    {
        $dvir = new Dvir([
            'safetyStatus' => 'unsafe',
        ]);

        $this->assertTrue($dvir->isUnsafe());
        $this->assertFalse($dvir->isSafe());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'           => '7107471',
            'type'         => 'preTrip',
            'safetyStatus' => 'safe',
        ];

        $dvir = new Dvir($data);

        $this->assertSame($data, $dvir->toArray());
    }

    #[Test]
    public function it_can_have_location(): void
    {
        $dvir = new Dvir([
            'location' => [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $this->assertSame(37.7749, $dvir->location['latitude']);
    }

    #[Test]
    public function it_can_have_mechanic_notes(): void
    {
        $dvir = new Dvir([
            'mechanicNotes' => 'Fixed the brake issue',
        ]);

        $this->assertSame('Fixed the brake issue', $dvir->mechanicNotes);
    }

    #[Test]
    public function it_can_have_odometer(): void
    {
        $dvir = new Dvir([
            'odometerMeters' => 150000,
        ]);

        $this->assertSame(150000, $dvir->odometerMeters);
    }

    #[Test]
    public function it_can_have_signatures(): void
    {
        $dvir = new Dvir([
            'authorSignature' => ['signedAtTime' => '2024-04-10T07:30:00Z'],
            'secondSignature' => ['signedAtTime' => '2024-04-10T08:00:00Z'],
        ]);

        $this->assertSame('2024-04-10T07:30:00Z', $dvir->authorSignature['signedAtTime']);
        $this->assertSame('2024-04-10T08:00:00Z', $dvir->secondSignature['signedAtTime']);
    }

    #[Test]
    public function it_can_have_trailer(): void
    {
        $dvir = new Dvir([
            'trailer' => [
                'id'   => 'trailer-1',
                'name' => 'Trailer A',
            ],
            'trailerName' => 'Midwest Trailer #5',
        ]);

        $this->assertSame('trailer-1', $dvir->trailer['id']);
        $this->assertSame('Midwest Trailer #5', $dvir->trailerName);
    }

    #[Test]
    public function it_can_have_trailer_defects(): void
    {
        $dvir = new Dvir([
            'trailerDefects' => [
                ['id' => 'defect-1', 'defectType' => 'Brake'],
            ],
        ]);

        $this->assertCount(1, $dvir->trailerDefects);
    }

    #[Test]
    public function it_can_have_vehicle(): void
    {
        $dvir = new Dvir([
            'vehicle' => [
                'id'   => 'vehicle-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('vehicle-1', $dvir->vehicle['id']);
    }

    #[Test]
    public function it_can_have_vehicle_defects(): void
    {
        $dvir = new Dvir([
            'vehicleDefects' => [
                ['id' => 'defect-1', 'defectType' => 'Air Compressor'],
            ],
        ]);

        $this->assertCount(1, $dvir->vehicleDefects);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $dvir = new Dvir;

        $this->assertInstanceOf(Entity::class, $dvir);
    }
}

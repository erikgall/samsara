<?php

namespace Samsara\Tests\Unit\Data\Safety;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Safety\SafetyScore;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the SafetyScore entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SafetyScoreTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $score = new SafetyScore([
            'driverId'              => 'driver-1',
            'driverScore'           => 92,
            'driveDistanceMeters'   => 2207296,
            'driveTimeMilliseconds' => 136997730,
        ]);

        $this->assertSame('driver-1', $score->driverId);
        $this->assertSame(92, $score->driverScore);
        $this->assertSame(2207296, $score->driveDistanceMeters);
        $this->assertSame(136997730, $score->driveTimeMilliseconds);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $score = SafetyScore::make([
            'driverId'    => 'driver-1',
            'driverScore' => 92,
        ]);

        $this->assertInstanceOf(SafetyScore::class, $score);
    }

    #[Test]
    public function it_can_check_if_score_is_excellent(): void
    {
        $score = new SafetyScore([
            'driverScore' => 90,
        ]);

        $this->assertTrue($score->isExcellent());
    }

    #[Test]
    public function it_can_check_if_score_is_good(): void
    {
        $score = new SafetyScore([
            'driverScore' => 75,
        ]);

        $this->assertTrue($score->isGood());
        $this->assertFalse($score->isExcellent());
    }

    #[Test]
    public function it_can_check_if_score_needs_improvement(): void
    {
        $score = new SafetyScore([
            'driverScore' => 50,
        ]);

        $this->assertTrue($score->needsImprovement());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'driverId'    => 'driver-1',
            'driverScore' => 92,
        ];

        $score = new SafetyScore($data);

        $this->assertSame($data, $score->toArray());
    }

    #[Test]
    public function it_can_get_drive_distance_in_kilometers(): void
    {
        $score = new SafetyScore([
            'driveDistanceMeters' => 150000,
        ]);

        $this->assertSame(150.0, $score->getDriveDistanceKilometers());
    }

    #[Test]
    public function it_can_get_drive_distance_in_miles(): void
    {
        $score = new SafetyScore([
            'driveDistanceMeters' => 160934,
        ]);

        $this->assertEqualsWithDelta(100.0, $score->getDriveDistanceMiles(), 0.1);
    }

    #[Test]
    public function it_can_get_drive_time_in_hours(): void
    {
        $score = new SafetyScore([
            'driveTimeMilliseconds' => 7200000, // 2 hours
        ]);

        $this->assertSame(2.0, $score->getDriveTimeHours());
    }

    #[Test]
    public function it_can_get_score(): void
    {
        $score = new SafetyScore([
            'driverScore' => 92,
        ]);

        $this->assertSame(92, $score->getScore());
    }

    #[Test]
    public function it_can_have_behaviors(): void
    {
        $score = new SafetyScore([
            'behaviors' => [
                ['type' => 'harshBrake', 'count' => 5],
                ['type' => 'harshAcceleration', 'count' => 3],
            ],
        ]);

        $this->assertCount(2, $score->behaviors);
    }

    #[Test]
    public function it_can_have_speeding(): void
    {
        $score = new SafetyScore([
            'speeding' => [
                ['type' => 'light', 'count' => 10],
                ['type' => 'moderate', 'count' => 2],
            ],
        ]);

        $this->assertCount(2, $score->speeding);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $score = new SafetyScore;

        $this->assertInstanceOf(Entity::class, $score);
    }

    #[Test]
    public function it_returns_null_for_drive_distance_when_not_set(): void
    {
        $score = new SafetyScore;

        $this->assertNull($score->getDriveDistanceKilometers());
        $this->assertNull($score->getDriveDistanceMiles());
    }

    #[Test]
    public function it_returns_null_for_drive_time_when_not_set(): void
    {
        $score = new SafetyScore;

        $this->assertNull($score->getDriveTimeHours());
    }

    #[Test]
    public function it_returns_null_for_score_when_not_set(): void
    {
        $score = new SafetyScore;

        $this->assertNull($score->getScore());
    }
}

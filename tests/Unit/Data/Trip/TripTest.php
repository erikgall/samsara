<?php

namespace Samsara\Tests\Unit\Data\Trip;

use Samsara\Data\Trip\Trip;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Trip entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TripTest extends TestCase
{
    #[Test]
    public function it_can_check_each_documented_completion_status(): void
    {
        $completed = new Trip(['completionStatus' => 'completed']);
        $inProgress = new Trip(['completionStatus' => 'inProgress']);

        $this->assertTrue($completed->isCompleted());
        $this->assertFalse($completed->isInProgress());
        $this->assertFalse($inProgress->isCompleted());
        $this->assertTrue($inProgress->isInProgress());
    }

    #[Test]
    public function it_can_have_asset(): void
    {
        $trip = new Trip([
            'asset' => [
                'id'   => 'asset-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('asset-1', $trip->asset['id']);
    }

    #[Test]
    public function it_can_have_end_location(): void
    {
        $trip = new Trip([
            'endLocation' => [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $this->assertSame(37.7749, $trip->endLocation['latitude']);
    }

    #[Test]
    public function it_can_have_start_location(): void
    {
        $trip = new Trip([
            'startLocation' => [
                'latitude'  => 34.0522,
                'longitude' => -118.2437,
            ],
        ]);

        $this->assertSame(34.0522, $trip->startLocation['latitude']);
    }

    #[Test]
    public function it_completion_status_checks_require_an_exact_known_value(): void
    {
        $attributes = [
            [],
            ['completionStatus' => null],
            ['completionStatus' => ''],
            ['completionStatus' => 'unknown'],
            ['completionStatus' => 'in progress'],
        ];

        foreach ($attributes as $values) {
            $trip = new Trip($values);

            $this->assertFalse($trip->isCompleted());
            $this->assertFalse($trip->isInProgress());
        }
    }

    #[Test]
    public function it_does_not_expose_helpers_for_fields_absent_from_the_current_contract(): void
    {
        $trip = new Trip;

        $this->assertFalse(method_exists($trip, 'getDistanceMiles'));
        $this->assertFalse(method_exists($trip, 'getDistanceKilometers'));
        $this->assertFalse(method_exists($trip, 'getDrivingTimeHours'));
        $this->assertFalse(method_exists($trip, 'getDrivingTimeMinutes'));
    }
}

<?php

namespace Samsara\Tests\Unit\Data\Route;

use Samsara\Tests\TestCase;
use Samsara\Data\Route\RouteStop;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the current RouteStop response entity.
 */
class RouteStopTest extends TestCase
{
    #[Test]
    public function it_can_check_each_documented_state(): void
    {
        $stateMethods = [
            'unassigned' => 'isUnassigned',
            'scheduled'  => 'isScheduled',
            'en route'   => 'isEnRoute',
            'skipped'    => 'isSkipped',
            'arrived'    => 'isArrived',
            'departed'   => 'isDeparted',
        ];

        foreach ($stateMethods as $state => $expectedMethod) {
            $stop = new RouteStop(['state' => $state]);

            foreach ($stateMethods as $method) {
                $this->assertSame($method === $expectedMethod, $stop->{$method}());
            }
        }
    }

    #[Test]
    public function it_converts_actual_and_planned_distances(): void
    {
        $stop = new RouteStop([
            'actualDistanceMeters'  => 1609344,
            'plannedDistanceMeters' => 3218688,
        ]);

        $this->assertSame(1609.344, $stop->getActualDistanceKilometers());
        $this->assertSame(1000.0, $stop->getActualDistanceMiles());
        $this->assertSame(3218.688, $stop->getPlannedDistanceKilometers());
        $this->assertSame(2000.0, $stop->getPlannedDistanceMiles());
    }

    #[Test]
    public function it_counts_orders(): void
    {
        $stop = new RouteStop([
            'orders' => [
                ['id' => 'order-1'],
                ['id' => 'order-2'],
            ],
        ]);

        $this->assertSame(2, $stop->getOrderCount());
    }

    #[Test]
    public function it_distance_conversions_preserve_zero_and_missing_values(): void
    {
        $zero = new RouteStop([
            'actualDistanceMeters'  => 0,
            'plannedDistanceMeters' => 0,
        ]);

        $this->assertSame(0.0, $zero->getActualDistanceKilometers());
        $this->assertSame(0.0, $zero->getActualDistanceMiles());
        $this->assertSame(0.0, $zero->getPlannedDistanceKilometers());
        $this->assertSame(0.0, $zero->getPlannedDistanceMiles());

        foreach ([new RouteStop, new RouteStop([
            'actualDistanceMeters'  => null,
            'plannedDistanceMeters' => null,
        ])] as $missing) {
            $this->assertNull($missing->getActualDistanceKilometers());
            $this->assertNull($missing->getActualDistanceMiles());
            $this->assertNull($missing->getPlannedDistanceKilometers());
            $this->assertNull($missing->getPlannedDistanceMiles());
        }
    }

    #[Test]
    public function it_documented_state_checks_require_an_exact_known_value(): void
    {
        $methods = [
            'isUnassigned',
            'isScheduled',
            'isEnRoute',
            'isSkipped',
            'isArrived',
            'isDeparted',
        ];
        $attributes = [
            [],
            ['state' => null],
            ['state' => ''],
            ['state' => 'unknown'],
            ['state' => 'enRoute'],
        ];

        foreach ($attributes as $values) {
            $stop = new RouteStop($values);

            foreach ($methods as $method) {
                $this->assertFalse($stop->{$method}());
            }
        }
    }

    #[Test]
    public function it_exposes_current_stop_detail_collections(): void
    {
        $stop = new RouteStop([
            'appointmentWindows'       => [['startTime' => '2026-08-10T14:00:00Z']],
            'documents'                => [['id' => 'document-1']],
            'forms'                    => [['id' => 'form-1']],
            'issues'                   => [['id' => 'issue-1']],
            'externalIds'              => ['customer' => 'stop-1'],
            'liveSharingUrl'           => 'https://cloud.samsara.com/live/stop-1',
            'locationLiveSharingLinks' => [['id' => 'share-1']],
        ]);

        $this->assertCount(1, $stop->appointmentWindows);
        $this->assertSame('form-1', $stop->forms[0]['id']);
        $this->assertSame('stop-1', $stop->externalIds['customer']);
    }

    #[Test]
    public function it_exposes_current_stop_fields_without_hub_location_helpers(): void
    {
        $stop = new RouteStop([
            'id'                    => 'stop-1',
            'name'                  => 'Current Stop',
            'state'                 => 'scheduled',
            'orders'                => [],
            'address'               => ['formattedAddress' => '123 Main Street'],
            'plannedDistanceMeters' => 1200,
            'actualDistanceMeters'  => 1250,
            'sequenceNumber'        => 1,
        ]);

        $this->assertSame('Current Stop', $stop->getDisplayName());
        $this->assertSame('scheduled', $stop->state);
        $this->assertSame(1200, $stop->plannedDistanceMeters);
        $this->assertSame(1250, $stop->actualDistanceMeters);
        $this->assertFalse(method_exists($stop, 'hasHubLocation'));
    }

    #[Test]
    public function it_handles_current_schedule_and_single_use_location_fields(): void
    {
        $stop = new RouteStop([
            'scheduledArrivalTime'   => '2026-08-10T14:30:00Z',
            'scheduledDepartureTime' => '2026-08-10T14:45:00Z',
            'singleUseLocation'      => ['address' => '123 Main Street'],
        ]);

        $this->assertSame('2026-08-10T14:30:00Z', $stop->scheduledArrivalTime);
        $this->assertSame('2026-08-10T14:45:00Z', $stop->scheduledDepartureTime);
        $this->assertTrue($stop->hasSingleUseLocation());
    }

    #[Test]
    public function it_returns_empty_defaults_when_values_are_not_set(): void
    {
        $stop = new RouteStop;

        $this->assertSame('Unknown', $stop->getDisplayName());
        $this->assertSame(0, $stop->getOrderCount());
        $this->assertFalse($stop->hasSingleUseLocation());
    }
}

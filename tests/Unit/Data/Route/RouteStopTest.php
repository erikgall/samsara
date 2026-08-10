<?php

namespace Samsara\Tests\Unit\Data\Route;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Route\RouteStop;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the current RouteStop response entity.
 */
class RouteStopTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $stop = RouteStop::make([
            'id'     => 'stop-1',
            'name'   => 'Current Stop',
            'state'  => 'scheduled',
            'orders' => [],
        ]);

        $this->assertInstanceOf(RouteStop::class, $stop);
        $this->assertSame('stop-1', $stop->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'     => 'stop-1',
            'name'   => 'Current Stop',
            'state'  => 'scheduled',
            'orders' => [],
        ];

        $this->assertSame($data, (new RouteStop($data))->toArray());
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
    public function it_extends_entity(): void
    {
        $this->assertInstanceOf(Entity::class, new RouteStop);
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

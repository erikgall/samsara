<?php

namespace Samsara\Tests\Unit\Data\Route;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Route\RouteStop;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the RouteStop entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RouteStopTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $stop = new RouteStop([
            'id'            => 'c3d4e5f6-a7b8-9012-cdef-345678901234',
            'name'          => 'Stop 1',
            'hubLocationId' => 'd4e5f6a7-b8c9-0123-defa-456789012345',
            'notes'         => 'Deliver to back door',
        ]);

        $this->assertSame('c3d4e5f6-a7b8-9012-cdef-345678901234', $stop->id);
        $this->assertSame('Stop 1', $stop->name);
        $this->assertSame('d4e5f6a7-b8c9-0123-defa-456789012345', $stop->hubLocationId);
        $this->assertSame('Deliver to back door', $stop->notes);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $stop = RouteStop::make([
            'id'   => 'c3d4e5f6-a7b8-9012-cdef-345678901234',
            'name' => 'Stop 1',
        ]);

        $this->assertInstanceOf(RouteStop::class, $stop);
        $this->assertSame('c3d4e5f6-a7b8-9012-cdef-345678901234', $stop->getId());
    }

    #[Test]
    public function it_can_check_if_has_hub_location(): void
    {
        $stop = new RouteStop([
            'hubLocationId' => 'd4e5f6a7-b8c9-0123-defa-456789012345',
        ]);

        $this->assertTrue($stop->hasHubLocation());
    }

    #[Test]
    public function it_can_check_if_has_single_use_location(): void
    {
        $stop = new RouteStop([
            'singleUseLocation' => [
                'address' => '123 Main St',
            ],
        ]);

        $this->assertTrue($stop->hasSingleUseLocation());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => 'c3d4e5f6-a7b8-9012-cdef-345678901234',
            'name' => 'Stop 1',
        ];

        $stop = new RouteStop($data);

        $this->assertSame($data, $stop->toArray());
    }

    #[Test]
    public function it_can_get_display_name(): void
    {
        $stop = new RouteStop([
            'name' => 'Customer Location',
        ]);

        $this->assertSame('Customer Location', $stop->getDisplayName());
    }

    #[Test]
    public function it_can_get_order_count(): void
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
    public function it_can_have_orders(): void
    {
        $stop = new RouteStop([
            'orders' => [
                ['id' => 'order-1', 'description' => 'Order 1'],
                ['id' => 'order-2', 'description' => 'Order 2'],
            ],
        ]);

        $this->assertCount(2, $stop->orders);
    }

    #[Test]
    public function it_can_have_scheduled_arrival_time(): void
    {
        $stop = new RouteStop([
            'scheduledArrivalTime' => '2024-04-10T09:00:00Z',
        ]);

        $this->assertSame('2024-04-10T09:00:00Z', $stop->scheduledArrivalTime);
    }

    #[Test]
    public function it_can_have_scheduled_departure_time(): void
    {
        $stop = new RouteStop([
            'scheduledDepartureTime' => '2024-04-10T09:30:00Z',
        ]);

        $this->assertSame('2024-04-10T09:30:00Z', $stop->scheduledDepartureTime);
    }

    #[Test]
    public function it_can_have_single_use_location(): void
    {
        $stop = new RouteStop([
            'singleUseLocation' => [
                'address'   => '123 Main St',
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $this->assertIsArray($stop->singleUseLocation);
        $this->assertSame('123 Main St', $stop->singleUseLocation['address']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $stop = new RouteStop;

        $this->assertInstanceOf(Entity::class, $stop);
    }

    #[Test]
    public function it_returns_false_when_no_hub_location(): void
    {
        $stop = new RouteStop;

        $this->assertFalse($stop->hasHubLocation());
    }

    #[Test]
    public function it_returns_false_when_no_single_use_location(): void
    {
        $stop = new RouteStop;

        $this->assertFalse($stop->hasSingleUseLocation());
    }

    #[Test]
    public function it_returns_unknown_for_missing_display_name(): void
    {
        $stop = new RouteStop;

        $this->assertSame('Unknown', $stop->getDisplayName());
    }

    #[Test]
    public function it_returns_zero_order_count_when_no_orders(): void
    {
        $stop = new RouteStop;

        $this->assertSame(0, $stop->getOrderCount());
    }
}

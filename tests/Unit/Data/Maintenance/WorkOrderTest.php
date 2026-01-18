<?php

namespace Samsara\Tests\Unit\Data\Maintenance;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Maintenance\WorkOrder;

/**
 * Unit tests for the WorkOrder entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WorkOrderTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $workOrder = new WorkOrder([
            'id'            => '5',
            'assetId'       => '12443',
            'status'        => 'Open',
            'priority'      => 'High',
            'description'   => 'The vehicle is not starting.',
            'createdAtTime' => '2024-04-10T07:06:25Z',
        ]);

        $this->assertSame('5', $workOrder->id);
        $this->assertSame('12443', $workOrder->assetId);
        $this->assertSame('Open', $workOrder->status);
        $this->assertSame('High', $workOrder->priority);
        $this->assertSame('The vehicle is not starting.', $workOrder->description);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $workOrder = WorkOrder::make([
            'id' => '5',
        ]);

        $this->assertInstanceOf(WorkOrder::class, $workOrder);
        $this->assertSame('5', $workOrder->getId());
    }

    #[Test]
    public function it_can_check_if_cancelled(): void
    {
        $workOrder = new WorkOrder([
            'status' => 'Cancelled',
        ]);

        $this->assertTrue($workOrder->isCancelled());
    }

    #[Test]
    public function it_can_check_if_closed(): void
    {
        $workOrder = new WorkOrder([
            'status' => 'Closed',
        ]);

        $this->assertTrue($workOrder->isClosed());
    }

    #[Test]
    public function it_can_check_if_completed(): void
    {
        $workOrder = new WorkOrder([
            'status' => 'Completed',
        ]);

        $this->assertTrue($workOrder->isCompleted());
        $this->assertFalse($workOrder->isOpen());
    }

    #[Test]
    public function it_can_check_if_high_priority(): void
    {
        $workOrder = new WorkOrder([
            'priority' => 'High',
        ]);

        $this->assertTrue($workOrder->isHighPriority());
    }

    #[Test]
    public function it_can_check_if_in_progress(): void
    {
        $workOrder = new WorkOrder([
            'status' => 'In Progress',
        ]);

        $this->assertTrue($workOrder->isInProgress());
    }

    #[Test]
    public function it_can_check_if_open(): void
    {
        $workOrder = new WorkOrder([
            'status' => 'Open',
        ]);

        $this->assertTrue($workOrder->isOpen());
        $this->assertFalse($workOrder->isCompleted());
        $this->assertFalse($workOrder->isClosed());
    }

    #[Test]
    public function it_can_check_if_urgent_priority(): void
    {
        $workOrder = new WorkOrder([
            'priority' => 'Urgent',
        ]);

        $this->assertTrue($workOrder->isUrgent());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'       => '5',
            'status'   => 'Open',
            'priority' => 'High',
        ];

        $workOrder = new WorkOrder($data);

        $this->assertSame($data, $workOrder->toArray());
    }

    #[Test]
    public function it_can_have_assigned_user(): void
    {
        $workOrder = new WorkOrder([
            'assignedUserId' => '1234',
        ]);

        $this->assertSame('1234', $workOrder->assignedUserId);
    }

    #[Test]
    public function it_can_have_closing_notes(): void
    {
        $workOrder = new WorkOrder([
            'closingNotes' => 'Everything was fixed without issues.',
        ]);

        $this->assertSame('Everything was fixed without issues.', $workOrder->closingNotes);
    }

    #[Test]
    public function it_can_have_invoice_and_po_numbers(): void
    {
        $workOrder = new WorkOrder([
            'invoiceNumber' => '123456',
            'poNumber'      => '789012',
        ]);

        $this->assertSame('123456', $workOrder->invoiceNumber);
        $this->assertSame('789012', $workOrder->poNumber);
    }

    #[Test]
    public function it_can_have_items(): void
    {
        $workOrder = new WorkOrder([
            'items' => [
                ['name' => 'Oil Filter', 'quantity' => 1],
                ['name' => 'Brake Pads', 'quantity' => 4],
            ],
        ]);

        $this->assertCount(2, $workOrder->items);
    }

    #[Test]
    public function it_can_have_odometer_and_engine_hours(): void
    {
        $workOrder = new WorkOrder([
            'odometerMeters' => 91823,
            'engineHours'    => 500,
        ]);

        $this->assertSame(91823, $workOrder->odometerMeters);
        $this->assertSame(500, $workOrder->engineHours);
    }

    #[Test]
    public function it_can_have_service_task_instances(): void
    {
        $workOrder = new WorkOrder([
            'serviceTaskInstances' => [
                ['id' => 'task-1', 'name' => 'Oil Change'],
            ],
        ]);

        $this->assertCount(1, $workOrder->serviceTaskInstances);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $workOrder = new WorkOrder([
            'createdAtTime'   => '2024-04-10T07:06:25Z',
            'updatedAtTime'   => '2024-04-11T10:00:00Z',
            'completedAtTime' => '2024-04-12T15:00:00Z',
            'dueAtTime'       => '2024-04-15T17:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $workOrder->createdAtTime);
        $this->assertSame('2024-04-11T10:00:00Z', $workOrder->updatedAtTime);
        $this->assertSame('2024-04-12T15:00:00Z', $workOrder->completedAtTime);
        $this->assertSame('2024-04-15T17:00:00Z', $workOrder->dueAtTime);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $workOrder = new WorkOrder;

        $this->assertInstanceOf(Entity::class, $workOrder);
    }
}

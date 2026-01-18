<?php

namespace Samsara\Tests\Unit\Data\Maintenance;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Maintenance\ServiceTask;

/**
 * Unit tests for the ServiceTask entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ServiceTaskTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $serviceTask = new ServiceTask([
            'id'                    => '12345',
            'name'                  => 'Oil Change',
            'description'           => 'Standard oil change service',
            'estimatedLaborHours'   => 1.5,
            'estimatedPartsCostUsd' => 50.00,
        ]);

        $this->assertSame('12345', $serviceTask->id);
        $this->assertSame('Oil Change', $serviceTask->name);
        $this->assertSame('Standard oil change service', $serviceTask->description);
        $this->assertSame(1.5, $serviceTask->estimatedLaborHours);
        $this->assertSame(50.00, $serviceTask->estimatedPartsCostUsd);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $serviceTask = ServiceTask::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(ServiceTask::class, $serviceTask);
        $this->assertSame('12345', $serviceTask->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'          => '12345',
            'name'        => 'Oil Change',
            'description' => 'Standard oil change service',
        ];

        $serviceTask = new ServiceTask($data);

        $this->assertSame($data, $serviceTask->toArray());
    }

    #[Test]
    public function it_can_have_actual_values(): void
    {
        $serviceTask = new ServiceTask([
            'actualLaborHours'   => 2.0,
            'actualPartsCostUsd' => 75.00,
        ]);

        $this->assertSame(2.0, $serviceTask->actualLaborHours);
        $this->assertSame(75.00, $serviceTask->actualPartsCostUsd);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $serviceTask = new ServiceTask([
            'createdAtTime' => '2024-04-10T07:06:25Z',
            'updatedAtTime' => '2024-04-11T10:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $serviceTask->createdAtTime);
        $this->assertSame('2024-04-11T10:00:00Z', $serviceTask->updatedAtTime);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $serviceTask = new ServiceTask;

        $this->assertInstanceOf(Entity::class, $serviceTask);
    }
}

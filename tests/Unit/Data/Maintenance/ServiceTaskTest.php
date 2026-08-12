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
            'id'                        => '12345',
            'name'                      => 'Oil Change',
            'description'               => 'Standard oil change service',
            'estimatedLaborTimeMinutes' => 90,
            'estimatedPartsCost'        => ['amount' => 50.00, 'currency' => 'USD'],
        ]);

        $this->assertSame('12345', $serviceTask->id);
        $this->assertSame('Oil Change', $serviceTask->name);
        $this->assertSame('Standard oil change service', $serviceTask->description);
        $this->assertSame(90, $serviceTask->estimatedLaborTimeMinutes);
        $this->assertSame(50.00, $serviceTask->estimatedPartsCost['amount']);
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
    public function it_can_have_category_values(): void
    {
        $serviceTask = new ServiceTask([
            'category'    => 'Engine',
            'subcategory' => 'Oil and Lubrication',
        ]);

        $this->assertSame('Engine', $serviceTask->category);
        $this->assertSame('Oil and Lubrication', $serviceTask->subcategory);
    }

    #[Test]
    public function it_converts_estimated_labor_minutes_to_hours(): void
    {
        $this->assertSame(1.5, (new ServiceTask(['estimatedLaborTimeMinutes' => 90]))->getEstimatedLaborHours());
        $this->assertSame(0.0, (new ServiceTask(['estimatedLaborTimeMinutes' => 0]))->getEstimatedLaborHours());
        $this->assertNull((new ServiceTask)->getEstimatedLaborHours());
        $this->assertNull((new ServiceTask(['estimatedLaborTimeMinutes' => null]))->getEstimatedLaborHours());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $serviceTask = new ServiceTask;

        $this->assertInstanceOf(Entity::class, $serviceTask);
    }
}

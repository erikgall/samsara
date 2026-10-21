<?php

namespace Samsara\Tests\Unit\Data\Maintenance;

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
}

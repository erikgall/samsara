<?php

namespace Samsara\Tests\Unit\Data\Vehicle;

use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Vehicle\VehicleStatValue;
use Samsara\Data\Vehicle\VehicleStatsSeries;

class VehicleStatsSeriesTest extends TestCase
{
    #[Test]
    public function it_gets_an_exact_string_external_id(): void
    {
        $series = new VehicleStatsSeries([
            'externalIds' => ['fleet' => 'vehicle-123'],
        ]);

        $this->assertSame('vehicle-123', $series->getExternalId('fleet'));
        $this->assertNull($series->getExternalId('missing'));
        $this->assertNull((new VehicleStatsSeries)->getExternalId('fleet'));
    }

    #[Test]
    public function it_gets_typed_engine_rpm_readings(): void
    {
        $series = new VehicleStatsSeries([
            'engineRpm' => [
                ['time' => '2026-08-10T12:00:00Z', 'value' => 1800],
                ['time' => '2026-08-10T12:01:00Z', 'value' => 1900],
            ],
        ]);

        $readings = $series->getEngineRpmReadings();

        $this->assertInstanceOf(EntityCollection::class, $readings);
        $this->assertCount(2, $readings);
        $this->assertInstanceOf(VehicleStatValue::class, $readings->first());
        $this->assertSame(1800, $readings->first()->value);

        foreach ([new VehicleStatsSeries, new VehicleStatsSeries(['engineRpm' => null]), new VehicleStatsSeries(['engineRpm' => 'invalid'])] as $empty) {
            $this->assertCount(0, $empty->getEngineRpmReadings());
        }
    }
}

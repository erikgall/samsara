<?php

namespace Samsara\Tests\Unit\Data\Vehicle;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Vehicle\VehicleStatValue;

class VehicleStatValueTest extends TestCase
{
    #[Test]
    public function it_gets_a_complete_decoration_by_exact_key(): void
    {
        $reading = new VehicleStatValue([
            'decorations' => [
                'engineStates' => ['value' => 'On'],
                'gps'          => ['latitude' => 37.7749, 'longitude' => -122.4194],
                'empty'        => [],
            ],
        ]);

        $this->assertSame(['value' => 'On'], $reading->getDecoration('engineStates'));
        $this->assertSame(['latitude' => 37.7749, 'longitude' => -122.4194], $reading->getDecoration('gps'));
        $this->assertSame([], $reading->getDecoration('empty'));
        $this->assertNull($reading->getDecoration('missing'));
        $this->assertNull((new VehicleStatValue)->getDecoration('engineStates'));
        $this->assertNull((new VehicleStatValue(['decorations' => null]))->getDecoration('engineStates'));
    }
}

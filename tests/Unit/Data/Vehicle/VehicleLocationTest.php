<?php

namespace Samsara\Tests\Unit\Data\Vehicle;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Vehicle\VehicleLocation;

class VehicleLocationTest extends TestCase
{
    #[Test]
    public function it_gets_coordinates_with_partial_data(): void
    {
        $location = new VehicleLocation([
            'latitude' => 37.7749,
        ]);

        $this->assertSame([
            'latitude'  => 37.7749,
            'longitude' => null,
        ], $location->getCoordinates());
        $this->assertSame([
            'latitude'  => null,
            'longitude' => null,
        ], (new VehicleLocation)->getCoordinates());
    }
}

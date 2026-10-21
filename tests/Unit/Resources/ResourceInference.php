<?php

namespace Samsara\Tests\Unit\Resources;

use Samsara\Samsara;

use function PHPStan\Testing\assertType;

function assertResourceInference(Samsara $samsara): void
{
    assertType('Samsara\Data\Driver\Driver|null', $samsara->drivers()->find('1'));
    assertType('Samsara\Data\Driver\Driver', $samsara->drivers()->create(['name' => 'Driver']));
    assertType('Samsara\Data\Driver\Driver', $samsara->drivers()->update('1', ['name' => 'Driver']));
    assertType('Samsara\Data\Driver\Driver|null', $samsara->drivers()->query()->first());
    assertType('Samsara\Data\Vehicle\VehicleLocationSeries|null', $samsara->vehicleLocations()->history()->first());
    assertType('Samsara\Data\HoursOfService\HosLogsForDriver|null', $samsara->hoursOfService()->logs()->lazy()->first());

    foreach ($samsara->hoursOfService()->logs()->lazy() as $logs) {
        assertType('Samsara\Data\HoursOfService\HosLogsForDriver', $logs);
    }
}

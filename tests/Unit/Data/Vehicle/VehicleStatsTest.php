<?php

namespace Samsara\Tests\Unit\Data\Vehicle;

use Samsara\Tests\TestCase;
use Samsara\Enums\EngineState;
use Samsara\Data\Vehicle\GpsLocation;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Vehicle\VehicleStats;

/**
 * Unit tests for the VehicleStats entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleStatsTest extends TestCase
{
    #[Test]
    public function it_can_check_if_engine_is_idle(): void
    {
        $stats = new VehicleStats([
            'engineState' => [
                'value' => 'Idle',
            ],
        ]);

        $this->assertTrue($stats->isEngineIdle());
    }

    #[Test]
    public function it_can_check_if_engine_is_off(): void
    {
        $stats = new VehicleStats([
            'engineState' => [
                'value' => 'Off',
            ],
        ]);

        $this->assertTrue($stats->isEngineOff());
    }

    #[Test]
    public function it_can_check_if_engine_is_on(): void
    {
        $stats = new VehicleStats([
            'engineState' => [
                'value' => 'On',
            ],
        ]);

        $this->assertTrue($stats->isEngineOn());
    }

    #[Test]
    public function it_can_get_engine_state_enum(): void
    {
        $stats = new VehicleStats([
            'engineState' => [
                'value' => 'On',
            ],
        ]);

        $state = $stats->getEngineState();

        $this->assertInstanceOf(EngineState::class, $state);
        $this->assertSame(EngineState::ON, $state);
    }

    #[Test]
    public function it_can_get_fuel_percent_value(): void
    {
        $stats = new VehicleStats([
            'fuelPercent' => [
                'value' => 75,
            ],
        ]);

        $this->assertSame(75, $stats->getFuelPercent());
    }

    #[Test]
    public function it_can_get_gps_as_entity(): void
    {
        $stats = new VehicleStats([
            'gps' => [
                'latitude'          => 37.765363,
                'longitude'         => -122.4029238,
                'headingDegrees'    => 180,
                'speedMilesPerHour' => 65.5,
                'time'              => '2024-01-15T10:00:00Z',
            ],
        ]);

        $gps = $stats->getGps();

        $this->assertInstanceOf(GpsLocation::class, $gps);
        $this->assertSame(37.765363, $gps->latitude);
        $this->assertSame(-122.4029238, $gps->longitude);
    }

    #[Test]
    public function it_can_have_battery_voltage(): void
    {
        $stats = new VehicleStats([
            'batteryMilliVolts' => [
                'value' => 12600,
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertSame(12600, $stats->getBatteryMilliVolts());
    }

    #[Test]
    public function it_can_have_def_level(): void
    {
        $stats = new VehicleStats([
            'defLevelMilliPercent' => [
                'value' => 85000,
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertSame(85000, $stats->getDefLevelMilliPercent());
    }

    #[Test]
    public function it_can_have_engine_coolant_temperature(): void
    {
        $stats = new VehicleStats([
            'engineCoolantTemperatureMilliC' => [
                'value' => 90000,
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertSame(90000, $stats->getEngineCoolantTemperatureMilliC());
    }

    #[Test]
    public function it_can_have_engine_rpm(): void
    {
        $stats = new VehicleStats([
            'engineRpm' => [
                'value' => 2500,
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertSame(2500, $stats->getEngineRpm());
    }

    #[Test]
    public function it_can_have_engine_state(): void
    {
        $stats = new VehicleStats([
            'engineState' => [
                'value' => 'On',
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertIsArray($stats->engineState);
    }

    #[Test]
    public function it_can_have_fuel_percent(): void
    {
        $stats = new VehicleStats([
            'fuelPercent' => [
                'value' => 75,
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertIsArray($stats->fuelPercent);
    }

    #[Test]
    public function it_can_have_gps_data(): void
    {
        $stats = new VehicleStats([
            'gps' => [
                'latitude'          => 37.765363,
                'longitude'         => -122.4029238,
                'headingDegrees'    => 180,
                'speedMilesPerHour' => 65.5,
                'time'              => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertIsArray($stats->gps);
    }

    #[Test]
    public function it_can_have_gps_odometer_meters(): void
    {
        $stats = new VehicleStats([
            'gpsOdometerMeters' => [
                'value' => 145000000,
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertSame(145000000, $stats->getGpsOdometerMeters());
    }

    #[Test]
    public function it_can_have_obd_engine_seconds(): void
    {
        $stats = new VehicleStats([
            'obdEngineSeconds' => [
                'value' => 3600000,
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertSame(3600000, $stats->getObdEngineSeconds());
    }

    #[Test]
    public function it_can_have_obd_odometer_meters(): void
    {
        $stats = new VehicleStats([
            'obdOdometerMeters' => [
                'value' => 150000000,
                'time'  => '2024-01-15T10:00:00Z',
            ],
        ]);

        $this->assertSame(150000000, $stats->getObdOdometerMeters());
    }

    #[Test]
    public function it_gets_an_exact_string_external_id(): void
    {
        $stats = new VehicleStats([
            'externalIds' => [
                'fleet' => 'vehicle-123',
                'empty' => '',
            ],
        ]);

        $this->assertSame('vehicle-123', $stats->getExternalId('fleet'));
        $this->assertSame('', $stats->getExternalId('empty'));
        $this->assertNull($stats->getExternalId('missing'));
        $this->assertNull((new VehicleStats)->getExternalId('fleet'));
        $this->assertNull((new VehicleStats(['externalIds' => null]))->getExternalId('fleet'));
    }

    #[Test]
    public function it_returns_null_engine_state_when_not_set(): void
    {
        $stats = new VehicleStats;

        $this->assertNull($stats->getEngineState());
    }

    #[Test]
    public function it_returns_null_for_an_unknown_engine_state(): void
    {
        foreach (['unknown', '', null] as $state) {
            $attributes = ['engineState' => ['value' => $state, 'time' => '2026-08-10T12:00:00Z']];
            $stats = VehicleStats::make($attributes);

            $this->assertNull($stats->getEngineState());
            $this->assertFalse($stats->isEngineOn());
            $this->assertFalse($stats->isEngineOff());
            $this->assertFalse($stats->isEngineIdle());
            $this->assertSame($attributes, $stats->toArray());
        }
    }

    #[Test]
    public function it_returns_null_fuel_percent_when_not_set(): void
    {
        $stats = new VehicleStats;

        $this->assertNull($stats->getFuelPercent());
    }

    #[Test]
    public function it_returns_null_gps_when_not_set(): void
    {
        $stats = new VehicleStats;

        $this->assertNull($stats->getGps());
    }
}

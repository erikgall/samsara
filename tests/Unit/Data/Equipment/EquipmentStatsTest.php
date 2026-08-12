<?php

namespace Samsara\Tests\Unit\Data\Equipment;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Enums\EngineState;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Equipment\EquipmentStats;
use Samsara\Data\Equipment\EquipmentStatValue;
use Samsara\Data\Equipment\EquipmentGpsLocation;
use Samsara\Data\Equipment\EquipmentStatsSeries;

/**
 * Unit tests for the EquipmentStats entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentStatsTest extends TestCase
{
    #[Test]
    public function it_can_access_basic_properties(): void
    {
        $stats = new EquipmentStats([
            'id'   => 'equip-123',
            'name' => 'Excavator 1',
        ]);

        $this->assertSame('equip-123', $stats->id);
        $this->assertSame('Excavator 1', $stats->name);
    }

    #[Test]
    public function it_can_check_if_engine_is_idle_via_obd(): void
    {
        $stats = new EquipmentStats([
            'obdEngineState' => [
                'value' => 'Idle',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertTrue($stats->isObdEngineIdle());
    }

    #[Test]
    public function it_can_check_if_engine_is_off_via_gateway(): void
    {
        $stats = new EquipmentStats([
            'gatewayEngineState' => [
                'value' => 'Off',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertTrue($stats->isGatewayEngineOff());
        $this->assertFalse($stats->isGatewayEngineOn());
    }

    #[Test]
    public function it_can_check_if_engine_is_on_via_gateway(): void
    {
        $stats = new EquipmentStats([
            'gatewayEngineState' => [
                'value' => 'On',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertTrue($stats->isGatewayEngineOn());
        $this->assertFalse($stats->isGatewayEngineOff());
    }

    #[Test]
    public function it_can_get_engine_rpm(): void
    {
        $stats = new EquipmentStats([
            'engineRpm' => [
                'value' => 2500,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(2500, $stats->getEngineRpm());
    }

    #[Test]
    public function it_can_get_engine_total_idle_time_minutes(): void
    {
        $stats = new EquipmentStats([
            'engineTotalIdleTimeMinutes' => [
                'value' => 120,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(120, $stats->getEngineTotalIdleTimeMinutes());
    }

    #[Test]
    public function it_can_get_fuel_percent(): void
    {
        $stats = new EquipmentStats([
            'fuelPercent' => [
                'value' => 75,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(75, $stats->getFuelPercent());
    }

    #[Test]
    public function it_can_get_gateway_engine_seconds(): void
    {
        $stats = new EquipmentStats([
            'gatewayEngineSeconds' => [
                'value' => 36000,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(36000, $stats->getGatewayEngineSeconds());
    }

    #[Test]
    public function it_can_get_gateway_engine_state(): void
    {
        $stats = new EquipmentStats([
            'gatewayEngineState' => [
                'value' => 'On',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(EngineState::ON, $stats->getGatewayEngineState());
    }

    #[Test]
    public function it_can_get_gps_data(): void
    {
        $stats = new EquipmentStats([
            'gps' => [
                'latitude'          => 37.7749,
                'longitude'         => -122.4194,
                'time'              => '2025-01-17T10:00:00Z',
                'headingDegrees'    => 180.0,
                'speedMilesPerHour' => 25.5,
                'address'           => ['name' => 'Main Yard'],
            ],
        ]);

        $gps = $stats->getGps();

        $this->assertNotNull($gps);
        $this->assertInstanceOf(EquipmentGpsLocation::class, $gps);
        $this->assertSame(37.7749, $gps->latitude);
        $this->assertSame(-122.4194, $gps->longitude);
        $this->assertSame(180.0, $gps->headingDegrees);
        $this->assertSame(25.5, $gps->speedMilesPerHour);
        $this->assertSame(['name' => 'Main Yard'], $gps->address);
    }

    #[Test]
    public function it_can_get_gps_odometer_meters(): void
    {
        $stats = new EquipmentStats([
            'gpsOdometerMeters' => [
                'value' => 150000,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(150000, $stats->getGpsOdometerMeters());
    }

    #[Test]
    public function it_can_get_obd_engine_seconds(): void
    {
        $stats = new EquipmentStats([
            'obdEngineSeconds' => [
                'value' => 72000,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(72000, $stats->getObdEngineSeconds());
    }

    #[Test]
    public function it_can_get_obd_engine_state(): void
    {
        $stats = new EquipmentStats([
            'obdEngineState' => [
                'value' => 'Idle',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(EngineState::IDLE, $stats->getObdEngineState());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $stats = new EquipmentStats([]);

        $this->assertInstanceOf(Entity::class, $stats);
    }

    #[Test]
    public function it_returns_null_for_invalid_engine_state(): void
    {
        $stats = new EquipmentStats([
            'gatewayEngineState' => [
                'value' => 'Unknown',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertNull($stats->getGatewayEngineState());
    }

    #[Test]
    public function it_returns_null_for_malformed_gps(): void
    {
        $this->assertNull((new EquipmentStats(['gps' => 'invalid']))->getGps());
    }

    #[Test]
    public function it_returns_null_for_missing_engine_rpm(): void
    {
        $stats = new EquipmentStats([]);

        $this->assertNull($stats->getEngineRpm());
    }

    #[Test]
    public function it_returns_null_for_missing_gps(): void
    {
        $stats = new EquipmentStats([]);

        $this->assertNull($stats->getGps());
    }

    #[Test]
    public function stat_series_expose_every_non_deprecated_scalar_series(): void
    {
        $methods = [
            'engineRpm'                  => 'getEngineRpmReadings',
            'engineTotalIdleTimeMinutes' => 'getEngineTotalIdleTimeMinutesReadings',
            'fuelPercents'               => 'getFuelPercentReadings',
            'gatewayEngineSeconds'       => 'getGatewayEngineSecondsReadings',
            'gatewayEngineStates'        => 'getGatewayEngineStateReadings',
            'gatewayJ1939EngineSeconds'  => 'getGatewayJ1939EngineSecondsReadings',
            'gpsOdometerMeters'          => 'getGpsOdometerMetersReadings',
            'obdEngineSeconds'           => 'getObdEngineSecondsReadings',
            'obdEngineStates'            => 'getObdEngineStateReadings',
        ];

        foreach ($methods as $key => $method) {
            $series = new EquipmentStatsSeries([
                $key => [['time' => '2026-08-12T12:00:00Z', 'value' => 42]],
            ]);
            $reading = $series->{$method}()->first();

            $this->assertInstanceOf(EquipmentStatValue::class, $reading);
            $this->assertSame('2026-08-12T12:00:00Z', $reading->time);
            $this->assertSame(42, $reading->value);
        }
    }

    #[Test]
    public function stat_series_expose_typed_gps_readings(): void
    {
        $series = new EquipmentStatsSeries([
            'gps' => [[
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
                'time'      => '2026-08-12T12:00:00Z',
            ]],
        ]);

        $this->assertInstanceOf(EquipmentGpsLocation::class, $series->getGpsReadings()->first());
    }

    #[Test]
    public function stat_series_return_empty_collections_for_invalid_inputs(): void
    {
        foreach ([null, 'invalid', []] as $readings) {
            $series = new EquipmentStatsSeries(['gatewayEngineStates' => $readings]);

            $this->assertTrue($series->getGatewayEngineStateReadings()->isEmpty());
        }

        $this->assertTrue((new EquipmentStatsSeries(['gps' => null]))->getGpsReadings()->isEmpty());
    }
}

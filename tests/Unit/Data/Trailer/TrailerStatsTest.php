<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Trailer;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Trailer\TrailerStats;

/**
 * Unit tests for the TrailerStats entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TrailerStatsTest extends TestCase
{
    #[Test]
    public function it_can_access_basic_properties(): void
    {
        $stats = new TrailerStats([
            'id'   => 'trailer-123',
            'name' => 'Reefer Trailer 1',
        ]);

        $this->assertSame('trailer-123', $stats->id);
        $this->assertSame('Reefer Trailer 1', $stats->name);
    }

    #[Test]
    public function it_can_check_if_door_is_open_zone1(): void
    {
        $open = new TrailerStats([
            'reeferDoorStateZone1' => [
                'value' => 'open',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $closed = new TrailerStats([
            'reeferDoorStateZone1' => [
                'value' => 'closed',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertTrue($open->isReeferDoorOpenZone1());
        $this->assertFalse($closed->isReeferDoorOpenZone1());
    }

    #[Test]
    public function it_can_check_if_reefer_is_running(): void
    {
        $running = new TrailerStats([
            'carrierReeferState' => [
                'value' => 'running',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $stopped = new TrailerStats([
            'carrierReeferState' => [
                'value' => 'stopped',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertTrue($running->isReeferRunning());
        $this->assertFalse($stopped->isReeferRunning());
    }

    #[Test]
    public function it_can_get_carrier_reefer_state(): void
    {
        $stats = new TrailerStats([
            'carrierReeferState' => [
                'value' => 'running',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame('running', $stats->getCarrierReeferState());
    }

    #[Test]
    public function it_can_get_gps_data(): void
    {
        $stats = new TrailerStats([
            'gps' => [
                'latitude'          => 40.7128,
                'longitude'         => -74.0060,
                'time'              => '2025-01-17T10:00:00Z',
                'headingDegrees'    => 90,
                'speedMilesPerHour' => 55.0,
            ],
        ]);

        $gps = $stats->getGps();

        $this->assertNotNull($gps);
        $this->assertSame(40.7128, $gps['latitude']);
        $this->assertSame(-74.0060, $gps['longitude']);
    }

    #[Test]
    public function it_can_get_gps_odometer_meters(): void
    {
        $stats = new TrailerStats([
            'gpsOdometerMeters' => [
                'value' => 250000,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(250000, $stats->getGpsOdometerMeters());
    }

    #[Test]
    public function it_can_get_reefer_ambient_air_temperature(): void
    {
        $stats = new TrailerStats([
            'reeferAmbientAirTemperatureMilliC' => [
                'value' => 25000, // 25 degrees Celsius
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(25000, $stats->getReeferAmbientAirTemperatureMilliC());
    }

    #[Test]
    public function it_can_get_reefer_door_state_zone1(): void
    {
        $stats = new TrailerStats([
            'reeferDoorStateZone1' => [
                'value' => 'open',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame('open', $stats->getReeferDoorStateZone1());
    }

    #[Test]
    public function it_can_get_reefer_fuel_percent(): void
    {
        $stats = new TrailerStats([
            'reeferFuelPercent' => [
                'value' => 85,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(85, $stats->getReeferFuelPercent());
    }

    #[Test]
    public function it_can_get_reefer_obd_engine_seconds(): void
    {
        $stats = new TrailerStats([
            'reeferObdEngineSeconds' => [
                'value' => 86400,
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(86400, $stats->getReeferObdEngineSeconds());
    }

    #[Test]
    public function it_can_get_reefer_return_air_temperature_zone1(): void
    {
        $stats = new TrailerStats([
            'reeferReturnAirTemperatureMilliCZone1' => [
                'value' => -18000, // -18 degrees Celsius
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(-18000, $stats->getReeferReturnAirTemperatureMilliCZone1());
    }

    #[Test]
    public function it_can_get_reefer_run_mode(): void
    {
        $stats = new TrailerStats([
            'reeferRunMode' => [
                'value' => 'continuous',
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame('continuous', $stats->getReeferRunMode());
    }

    #[Test]
    public function it_can_get_reefer_set_point_temperature_zone1(): void
    {
        $stats = new TrailerStats([
            'reeferSetPointTemperatureMilliCZone1' => [
                'value' => -20000, // -20 degrees Celsius
                'time'  => '2025-01-17T10:00:00Z',
            ],
        ]);

        $this->assertSame(-20000, $stats->getReeferSetPointTemperatureMilliCZone1());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $stats = new TrailerStats([]);

        $this->assertInstanceOf(Entity::class, $stats);
    }

    #[Test]
    public function it_returns_null_for_missing_gps_odometer(): void
    {
        $stats = new TrailerStats([]);

        $this->assertNull($stats->getGpsOdometerMeters());
    }
}

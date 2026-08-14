<?php

namespace Samsara\Tests\Unit\Enums;

use Samsara\Tests\TestCase;
use Samsara\Enums\VehicleStatType;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the VehicleStatType enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleStatTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $type = VehicleStatType::from('gps');

        $this->assertSame(VehicleStatType::GPS, $type);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $cases = VehicleStatType::cases();

        $this->assertGreaterThan(30, count($cases));
    }

    #[Test]
    public function it_has_engine_states_case(): void
    {
        $this->assertSame('engineStates', VehicleStatType::ENGINE_STATES->value);
    }

    #[Test]
    public function it_has_fuel_percents_case(): void
    {
        $this->assertSame('fuelPercents', VehicleStatType::FUEL_PERCENTS->value);
    }

    #[Test]
    public function it_has_gps_case(): void
    {
        $this->assertSame('gps', VehicleStatType::GPS->value);
    }

    #[Test]
    public function it_has_obd_odometer_meters_case(): void
    {
        $this->assertSame('obdOdometerMeters', VehicleStatType::OBD_ODOMETER_METERS->value);
    }

    #[Test]
    public function it_supports_every_current_vehicle_stat_query_value(): void
    {
        $currentValues = [
            'ambientAirTemperatureMilliC',
            'auxInput1',
            'auxInput2',
            'auxInput3',
            'auxInput4',
            'auxInput5',
            'auxInput6',
            'auxInput7',
            'auxInput8',
            'auxInput9',
            'auxInput10',
            'auxInput11',
            'auxInput12',
            'auxInput13',
            'barometricPressurePa',
            'batteryMilliVolts',
            'defLevelMilliPercent',
            'ecuSpeedMph',
            'engineCoolantTemperatureMilliC',
            'engineImmobilizer',
            'engineLoadPercent',
            'engineOilPressureKPa',
            'engineRpm',
            'engineStates',
            'faultCodes',
            'fuelPercents',
            'fuelConsumedMilliliters',
            'gps',
            'gpsDistanceMeters',
            'gpsOdometerMeters',
            'idlingDurationMilliseconds',
            'intakeManifoldTemperatureMilliC',
            'nfcCardScans',
            'obdEngineSeconds',
            'obdOdometerMeters',
            'syntheticEngineSeconds',
            'evStateOfChargeMilliPercent',
            'evChargingStatus',
            'evChargingEnergyMicroWh',
            'evChargingVoltageMilliVolt',
            'evChargingCurrentMilliAmp',
            'evConsumedEnergyMicroWh',
            'evRegeneratedEnergyMicroWh',
            'evBatteryVoltageMilliVolt',
            'evBatteryCurrentMilliAmp',
            'evBatteryStateOfHealthMilliPercent',
            'evAverageBatteryTemperatureMilliCelsius',
            'evDistanceDrivenMeters',
            'spreaderLiquidRate',
            'spreaderGranularRate',
            'spreaderPrewetRate',
            'spreaderAirTemp',
            'spreaderRoadTemp',
            'spreaderOnState',
            'spreaderActive',
            'spreaderBlastState',
            'spreaderGranularName',
            'spreaderPrewetName',
            'spreaderLiquidName',
            'spreaderPlowStatus',
            'seatbeltDriver',
            'tellTales',
            'ecuDoorStatus',
        ];
        $enumValues = array_map(
            fn (VehicleStatType $type) => $type->value,
            VehicleStatType::cases()
        );

        sort($currentValues);
        sort($enumValues);

        $this->assertSame($currentValues, $enumValues);
    }
}

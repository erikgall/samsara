<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * Vehicle stats feed or history result.
 *
 * The entity retains every requested official series key; typed helpers cover
 * common scalar and GPS readings without applying snapshot-only accessors.
 *
 * @property-read string|null $id Vehicle ID
 * @property-read string|null $name Vehicle name
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array<string, mixed>>|null $engineStates Engine-state readings
 * @property-read array<int, array<string, mixed>>|null $fuelPercents Fuel-percentage readings
 * @property-read array<int, array<string, mixed>>|null $gps GPS readings
 * @property-read array<int, array<string, mixed>>|null $ambientAirTemperatureMilliC Ambient air temperature readings
 * @property-read array<int, array<string, mixed>>|null $auxInput1 Auxiliary input 1 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput2 Auxiliary input 2 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput3 Auxiliary input 3 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput4 Auxiliary input 4 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput5 Auxiliary input 5 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput6 Auxiliary input 6 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput7 Auxiliary input 7 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput8 Auxiliary input 8 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput9 Auxiliary input 9 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput10 Auxiliary input 10 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput11 Auxiliary input 11 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput12 Auxiliary input 12 readings
 * @property-read array<int, array<string, mixed>>|null $auxInput13 Auxiliary input 13 readings
 * @property-read array<int, array<string, mixed>>|null $barometricPressurePa Barometric pressure readings
 * @property-read array<int, array<string, mixed>>|null $batteryMilliVolts Battery voltage readings
 * @property-read array<int, array<string, mixed>>|null $defLevelMilliPercent DEF level readings
 * @property-read array<int, array<string, mixed>>|null $ecuDoorStatus ECU door status readings
 * @property-read array<int, array<string, mixed>>|null $ecuSpeedMph ECU speed readings
 * @property-read array<int, array<string, mixed>>|null $engineCoolantTemperatureMilliC Engine coolant readings
 * @property-read array<int, array<string, mixed>>|null $engineImmobilizer Engine immobilizer readings
 * @property-read array<int, array<string, mixed>>|null $engineLoadPercent Engine load readings
 * @property-read array<int, array<string, mixed>>|null $engineOilPressureKPa Engine oil pressure readings
 * @property-read array<int, array<string, mixed>>|null $engineRpm Engine RPM readings
 * @property-read array<int, array<string, mixed>>|null $evAverageBatteryTemperatureMilliCelsius EV battery temperature readings
 * @property-read array<int, array<string, mixed>>|null $evBatteryCurrentMilliAmp EV battery current readings
 * @property-read array<int, array<string, mixed>>|null $evBatteryStateOfHealthMilliPercent EV state-of-health readings
 * @property-read array<int, array<string, mixed>>|null $evBatteryVoltageMilliVolt EV battery voltage readings
 * @property-read array<int, array<string, mixed>>|null $evChargingCurrentMilliAmp EV charging current readings
 * @property-read array<int, array<string, mixed>>|null $evChargingEnergyMicroWh EV charging energy readings
 * @property-read array<int, array<string, mixed>>|null $evChargingStatus EV charging status readings
 * @property-read array<int, array<string, mixed>>|null $evChargingVoltageMilliVolt EV charging voltage readings
 * @property-read array<int, array<string, mixed>>|null $evConsumedEnergyMicroWh EV consumed energy readings
 * @property-read array<int, array<string, mixed>>|null $evDistanceDrivenMeters EV distance readings
 * @property-read array<int, array<string, mixed>>|null $evRegeneratedEnergyMicroWh EV regenerated energy readings
 * @property-read array<int, array<string, mixed>>|null $evStateOfChargeMilliPercent EV charge readings
 * @property-read array<int, array<string, mixed>>|null $faultCodes Fault-code readings
 * @property-read array<int, array<string, mixed>>|null $fuelConsumedMilliliters Fuel-consumption readings
 * @property-read array<int, array<string, mixed>>|null $gpsDistanceMeters GPS distance readings
 * @property-read array<int, array<string, mixed>>|null $gpsOdometerMeters GPS odometer readings
 * @property-read array<int, array<string, mixed>>|null $idlingDurationMilliseconds Idling readings
 * @property-read array<int, array<string, mixed>>|null $intakeManifoldTemperatureMilliC Intake temperature readings
 * @property-read array<int, array<string, mixed>>|null $nfcCardScans NFC card readings
 * @property-read array<int, array<string, mixed>>|null $obdEngineSeconds OBD engine-second readings
 * @property-read array<int, array<string, mixed>>|null $obdOdometerMeters OBD odometer readings
 * @property-read array<int, array<string, mixed>>|null $seatbeltDriver Driver seatbelt readings
 * @property-read array<int, array<string, mixed>>|null $spreaderActive Spreader active readings
 * @property-read array<int, array<string, mixed>>|null $spreaderAirTemp Spreader air-temperature readings
 * @property-read array<int, array<string, mixed>>|null $spreaderBlastState Spreader blast readings
 * @property-read array<int, array<string, mixed>>|null $spreaderGranularName Spreader granular-name readings
 * @property-read array<int, array<string, mixed>>|null $spreaderGranularRate Spreader granular-rate readings
 * @property-read array<int, array<string, mixed>>|null $spreaderLiquidName Spreader liquid-name readings
 * @property-read array<int, array<string, mixed>>|null $spreaderLiquidRate Spreader liquid-rate readings
 * @property-read array<int, array<string, mixed>>|null $spreaderOnState Spreader on-state readings
 * @property-read array<int, array<string, mixed>>|null $spreaderPlowStatus Spreader plow readings
 * @property-read array<int, array<string, mixed>>|null $spreaderPrewetName Spreader prewet-name readings
 * @property-read array<int, array<string, mixed>>|null $spreaderPrewetRate Spreader prewet-rate readings
 * @property-read array<int, array<string, mixed>>|null $spreaderRoadTemp Spreader road-temperature readings
 * @property-read array<int, array<string, mixed>>|null $syntheticEngineSeconds Synthetic engine-second readings
 */
class VehicleStatsSeries extends Entity
{
    /**
     * @return EntityCollection<int, VehicleStatValue>
     */
    public function getEngineStateReadings(): EntityCollection
    {
        return $this->statReadings('engineStates');
    }

    /**
     * @return EntityCollection<int, VehicleStatValue>
     */
    public function getFuelPercentReadings(): EntityCollection
    {
        return $this->statReadings('fuelPercents');
    }

    /**
     * @return EntityCollection<int, GpsLocation>
     */
    public function getGpsReadings(): EntityCollection
    {
        $readings = $this->get('gps', []);

        if (! is_array($readings)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $reading): GpsLocation => GpsLocation::make($reading),
            $readings
        ));
    }

    /**
     * @return EntityCollection<int, VehicleStatValue>
     */
    private function statReadings(string $key): EntityCollection
    {
        $readings = $this->get($key, []);

        if (! is_array($readings)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $reading): VehicleStatValue => VehicleStatValue::make($reading),
            $readings
        ));
    }
}

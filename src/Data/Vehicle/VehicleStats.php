<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;
use Samsara\Enums\EngineState;

/**
 * Vehicle stats entity.
 *
 * Represents telemetry/stats data for a vehicle.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Vehicle ID
 * @property-read string|null $name Vehicle name
 * @property-read array{latitude: float, longitude: float, headingDegrees?: int, speedMilesPerHour?: float, time: string}|null $gps GPS location data
 * @property-read array{value: string, time?: string}|null $engineState Engine state
 * @property-read array{value: int, time?: string}|null $fuelPercent Fuel percentage
 * @property-read array{value: int, time?: string}|null $obdOdometerMeters OBD odometer reading
 * @property-read array{value: int, time?: string}|null $gpsOdometerMeters GPS odometer reading
 * @property-read array{value: int, time?: string}|null $engineRpm Engine RPM
 * @property-read array{value: int, time?: string}|null $batteryMilliVolts Battery voltage
 * @property-read array{value: int, time?: string}|null $engineCoolantTemperatureMilliC Engine coolant temperature
 * @property-read array{value: int, time?: string}|null $defLevelMilliPercent DEF level
 * @property-read array{value: int, time?: string}|null $obdEngineSeconds OBD engine seconds
 * @property-read array<string, mixed>|null $ambientAirTemperatureMilliC Ambient air temperature
 * @property-read array<string, mixed>|null $auxInput1 Auxiliary input 1
 * @property-read array<string, mixed>|null $auxInput2 Auxiliary input 2
 * @property-read array<string, mixed>|null $auxInput3 Auxiliary input 3
 * @property-read array<string, mixed>|null $auxInput4 Auxiliary input 4
 * @property-read array<string, mixed>|null $auxInput5 Auxiliary input 5
 * @property-read array<string, mixed>|null $auxInput6 Auxiliary input 6
 * @property-read array<string, mixed>|null $auxInput7 Auxiliary input 7
 * @property-read array<string, mixed>|null $auxInput8 Auxiliary input 8
 * @property-read array<string, mixed>|null $auxInput9 Auxiliary input 9
 * @property-read array<string, mixed>|null $auxInput10 Auxiliary input 10
 * @property-read array<string, mixed>|null $auxInput11 Auxiliary input 11
 * @property-read array<string, mixed>|null $auxInput12 Auxiliary input 12
 * @property-read array<string, mixed>|null $auxInput13 Auxiliary input 13
 * @property-read array<string, mixed>|null $barometricPressurePa Barometric pressure
 * @property-read array<string, mixed>|null $ecuDoorStatus ECU door status
 * @property-read array<string, mixed>|null $ecuSpeedMph ECU speed
 * @property-read array<string, mixed>|null $engineImmobilizer Engine immobilizer state
 * @property-read array<string, mixed>|null $engineLoadPercent Engine load
 * @property-read array<string, mixed>|null $engineOilPressureKPa Engine oil pressure
 * @property-read array<string, mixed>|null $evAverageBatteryTemperatureMilliCelsius EV average battery temperature
 * @property-read array<string, mixed>|null $evBatteryCurrentMilliAmp EV battery current
 * @property-read array<string, mixed>|null $evBatteryStateOfHealthMilliPercent EV battery state of health
 * @property-read array<string, mixed>|null $evBatteryVoltageMilliVolt EV battery voltage
 * @property-read array<string, mixed>|null $evChargingCurrentMilliAmp EV charging current
 * @property-read array<string, mixed>|null $evChargingEnergyMicroWh EV charging energy
 * @property-read array<string, mixed>|null $evChargingStatus EV charging status
 * @property-read array<string, mixed>|null $evChargingVoltageMilliVolt EV charging voltage
 * @property-read array<string, mixed>|null $evConsumedEnergyMicroWh EV consumed energy
 * @property-read array<string, mixed>|null $evDistanceDrivenMeters EV distance driven
 * @property-read array<string, mixed>|null $evRegeneratedEnergyMicroWh EV regenerated energy
 * @property-read array<string, mixed>|null $evStateOfChargeMilliPercent EV state of charge
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<string, mixed>|null $faultCodes Fault codes
 * @property-read array<string, mixed>|null $fuelConsumedMilliliters Fuel consumed
 * @property-read array<string, mixed>|null $gpsDistanceMeters GPS distance
 * @property-read array<string, mixed>|null $idlingDurationMilliseconds Idling duration
 * @property-read array<string, mixed>|null $intakeManifoldTemperatureMilliC Intake manifold temperature
 * @property-read array<string, mixed>|null $nfcCardScan NFC card scan
 * @property-read array<string, mixed>|null $seatbeltDriver Driver seatbelt state
 * @property-read array<string, mixed>|null $spreaderActive Spreader active state
 * @property-read array<string, mixed>|null $spreaderAirTemp Spreader air temperature
 * @property-read array<string, mixed>|null $spreaderBlastState Spreader blast state
 * @property-read array<string, mixed>|null $spreaderGranularName Spreader granular name
 * @property-read array<string, mixed>|null $spreaderGranularRate Spreader granular rate
 * @property-read array<string, mixed>|null $spreaderLiquidName Spreader liquid name
 * @property-read array<string, mixed>|null $spreaderLiquidRate Spreader liquid rate
 * @property-read array<string, mixed>|null $spreaderOnState Spreader on state
 * @property-read array<string, mixed>|null $spreaderPlowStatus Spreader plow status
 * @property-read array<string, mixed>|null $spreaderPrewetName Spreader prewet name
 * @property-read array<string, mixed>|null $spreaderPrewetRate Spreader prewet rate
 * @property-read array<string, mixed>|null $spreaderRoadTemp Spreader road temperature
 * @property-read array<string, mixed>|null $syntheticEngineSeconds Synthetic engine seconds
 */
class VehicleStats extends Entity
{
    /**
     * Get the battery voltage in millivolts.
     */
    public function getBatteryMilliVolts(): ?int
    {
        return $this->batteryMilliVolts['value'] ?? null;
    }

    /**
     * Get the DEF level in milli-percent.
     */
    public function getDefLevelMilliPercent(): ?int
    {
        return $this->defLevelMilliPercent['value'] ?? null;
    }

    /**
     * Get the engine coolant temperature in milli-celsius.
     */
    public function getEngineCoolantTemperatureMilliC(): ?int
    {
        return $this->engineCoolantTemperatureMilliC['value'] ?? null;
    }

    /**
     * Get the engine RPM.
     */
    public function getEngineRpm(): ?int
    {
        return $this->engineRpm['value'] ?? null;
    }

    /**
     * Get the engine state as an enum.
     */
    public function getEngineState(): ?EngineState
    {
        $state = $this->engineState['value'] ?? null;

        if ($state === null) {
            return null;
        }

        return EngineState::tryFrom($state);
    }

    /**
     * Get the fuel percentage.
     */
    public function getFuelPercent(): ?int
    {
        return $this->fuelPercent['value'] ?? null;
    }

    /**
     * Get the GPS location as an entity.
     */
    public function getGps(): ?GpsLocation
    {
        $gps = $this->get('gps');

        if (empty($gps)) {
            return null;
        }

        return new GpsLocation($gps);
    }

    /**
     * Get the GPS odometer reading in meters.
     */
    public function getGpsOdometerMeters(): ?int
    {
        return $this->gpsOdometerMeters['value'] ?? null;
    }

    /**
     * Get the OBD engine seconds.
     */
    public function getObdEngineSeconds(): ?int
    {
        return $this->obdEngineSeconds['value'] ?? null;
    }

    /**
     * Get the OBD odometer reading in meters.
     */
    public function getObdOdometerMeters(): ?int
    {
        return $this->obdOdometerMeters['value'] ?? null;
    }

    /**
     * Check if the engine is currently idle.
     */
    public function isEngineIdle(): bool
    {
        return $this->getEngineState() === EngineState::IDLE;
    }

    /**
     * Check if the engine is currently off.
     */
    public function isEngineOff(): bool
    {
        return $this->getEngineState() === EngineState::OFF;
    }

    /**
     * Check if the engine is currently on.
     */
    public function isEngineOn(): bool
    {
        return $this->getEngineState() === EngineState::ON;
    }
}

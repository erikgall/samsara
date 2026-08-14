<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * Equipment stats feed or history result.
 *
 * @property-read string|null $id Equipment ID
 * @property-read string|null $name Equipment name
 * @property-read array<int, array<string, mixed>>|null $engineRpm Engine RPM readings
 * @property-read array<int, array<string, mixed>>|null $engineSeconds Engine-second readings
 * @property-read array<int, array<string, mixed>>|null $engineStates Engine-state readings
 * @property-read array<int, array<string, mixed>>|null $engineTotalIdleTimeMinutes Idle-time readings
 * @property-read array<int, array<string, mixed>>|null $fuelPercents Fuel-percentage readings
 * @property-read array<int, array<string, mixed>>|null $gatewayEngineSeconds Gateway engine-second readings
 * @property-read array<int, array<string, mixed>>|null $gatewayEngineStates Gateway engine-state readings
 * @property-read array<int, array<string, mixed>>|null $gatewayJ1939EngineSeconds J1939 engine-second readings
 * @property-read array<int, array<string, mixed>>|null $gps GPS readings
 * @property-read array<int, array<string, mixed>>|null $gpsOdometerMeters GPS odometer readings
 * @property-read array<int, array<string, mixed>>|null $obdEngineSeconds OBD engine-second readings
 * @property-read array<int, array<string, mixed>>|null $obdEngineStates OBD engine-state readings
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentStatsSeries extends Entity
{
    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getEngineRpmReadings(): EntityCollection
    {
        return $this->statReadings('engineRpm');
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getEngineTotalIdleTimeMinutesReadings(): EntityCollection
    {
        return $this->statReadings('engineTotalIdleTimeMinutes');
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getFuelPercentReadings(): EntityCollection
    {
        return $this->statReadings('fuelPercents');
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getGatewayEngineSecondsReadings(): EntityCollection
    {
        return $this->statReadings('gatewayEngineSeconds');
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getGatewayEngineStateReadings(): EntityCollection
    {
        return $this->statReadings('gatewayEngineStates');
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getGatewayJ1939EngineSecondsReadings(): EntityCollection
    {
        return $this->statReadings('gatewayJ1939EngineSeconds');
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getGpsOdometerMetersReadings(): EntityCollection
    {
        return $this->statReadings('gpsOdometerMeters');
    }

    /**
     * @return EntityCollection<int, EquipmentGpsLocation>
     */
    public function getGpsReadings(): EntityCollection
    {
        $readings = $this->get('gps', []);

        if (! is_array($readings)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $reading): EquipmentGpsLocation => EquipmentGpsLocation::make($reading),
            array_values(array_filter($readings, 'is_array'))
        ));
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getObdEngineSecondsReadings(): EntityCollection
    {
        return $this->statReadings('obdEngineSeconds');
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    public function getObdEngineStateReadings(): EntityCollection
    {
        return $this->statReadings('obdEngineStates');
    }

    /**
     * @return EntityCollection<int, EquipmentStatValue>
     */
    private function statReadings(string $key): EntityCollection
    {
        $readings = $this->get($key, []);

        if (! is_array($readings)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $reading): EquipmentStatValue => EquipmentStatValue::make($reading),
            array_values(array_filter($readings, 'is_array'))
        ));
    }
}

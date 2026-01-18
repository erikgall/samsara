<?php

namespace ErikGall\Samsara\Data\Vehicle;

use ErikGall\Samsara\Data\Entity;

/**
 * Vehicle entity.
 *
 * Represents a Samsara vehicle.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Vehicle ID
 * @property-read string|null $name Vehicle name
 * @property-read string|null $vin Vehicle Identification Number
 * @property-read string|null $make Vehicle make
 * @property-read string|null $model Vehicle model
 * @property-read string|null $year Vehicle year
 * @property-read string|null $licensePlate License plate number
 * @property-read string|null $notes Notes about the vehicle
 * @property-read string|null $serial Gateway serial number
 * @property-read string|null $esn Electronic Serial Number
 * @property-read string|null $vehicleType Vehicle type
 * @property-read string|null $cameraSerial Camera serial number
 * @property-read string|null $harshAccelerationSettingType Harsh acceleration setting type
 * @property-read string|null $vehicleRegulationMode Vehicle regulation mode
 * @property-read int|null $grossVehicleWeight Gross vehicle weight
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array{id: string, name?: string, parentTagId?: string}>|null $tags Associated tags
 * @property-read array{serial: string, model?: string}|null $gateway Gateway data
 * @property-read array{id: string, name?: string}|null $staticAssignedDriver Static assigned driver
 * @property-read array<int, array{id: string, name?: string, value?: string}>|null $attributes Vehicle attributes
 */
class Vehicle extends Entity
{
    /**
     * Get the display name for the vehicle.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? 'Unknown';
    }

    /**
     * Get an external ID by key.
     */
    public function getExternalId(string $key): ?string
    {
        $externalIds = $this->externalIds ?? [];

        return $externalIds[$key] ?? null;
    }

    /**
     * Get the gateway as an entity.
     */
    public function getGateway(): ?Gateway
    {
        $gateway = $this->get('gateway');

        if (empty($gateway)) {
            return null;
        }

        return new Gateway($gateway);
    }

    /**
     * Get the static assigned driver as an entity.
     */
    public function getStaticAssignedDriver(): ?StaticAssignedDriver
    {
        $driver = $this->get('staticAssignedDriver');

        if (empty($driver)) {
            return null;
        }

        return new StaticAssignedDriver($driver);
    }

    /**
     * Get all tag IDs associated with this vehicle.
     *
     * @return array<int, string>
     */
    public function getTagIds(): array
    {
        $tags = $this->tags ?? [];

        return array_map(fn (array $tag): string => (string) $tag['id'], $tags);
    }

    /**
     * Check if the vehicle has a static assigned driver.
     */
    public function hasStaticAssignedDriver(): bool
    {
        return ! empty($this->staticAssignedDriver);
    }
}

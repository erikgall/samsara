<?php

namespace ErikGall\Samsara\Data\Driver;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Enums\DriverActivationStatus;

/**
 * Driver entity.
 *
 * Represents a Samsara driver.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Driver ID
 * @property-read string|null $name Driver name
 * @property-read string|null $username Login username
 * @property-read string|null $phone Phone number
 * @property-read string|null $licenseNumber Driver's license number
 * @property-read string|null $licenseState License state abbreviation
 * @property-read string|null $timezone Home terminal timezone
 * @property-read string|null $notes Notes about the driver
 * @property-read string|null $createdAtTime Creation timestamp
 * @property-read string|null $updatedAtTime Last update timestamp
 * @property-read string|null $driverActivationStatus Activation status (active/deactivated)
 * @property-read string|null $profileImageUrl Profile image URL
 * @property-read string|null $locale Driver's locale
 * @property-read string|null $currentIdCardCode Current ID card code
 * @property-read string|null $tachographCardNumber Tachograph card number
 * @property-read string|null $eldExemptReason ELD exempt reason
 * @property-read bool|null $eldExempt Whether driver is ELD exempt
 * @property-read bool|null $eldPcEnabled Personal conveyance enabled
 * @property-read bool|null $eldYmEnabled Yard move enabled
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array{id: string, name?: string, parentTagId?: string}>|null $tags Associated tags
 * @property-read array{rulesets?: array<int, array{name?: string, cycle?: string}>}|null $eldSettings ELD settings
 * @property-read array{carrierName?: string, dotNumber?: int, homeTerminalAddress?: string, homeTerminalName?: string, mainOfficeAddress?: string}|null $carrierSettings Carrier settings
 * @property-read array{id: string, name?: string}|null $staticAssignedVehicle Static assigned vehicle
 */
class Driver extends Entity
{
    /**
     * Get the driver's activation status enum.
     */
    public function getActivationStatus(): ?DriverActivationStatus
    {
        $status = $this->driverActivationStatus;

        if ($status === null) {
            return null;
        }

        return DriverActivationStatus::tryFrom($status);
    }

    /**
     * Get the carrier settings as an entity.
     */
    public function getCarrierSettings(): ?CarrierSettings
    {
        $settings = $this->get('carrierSettings');

        if (empty($settings)) {
            return null;
        }

        return new CarrierSettings($settings);
    }

    /**
     * Get the display name for the driver.
     *
     * Falls back to username if name is not set.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? $this->username ?? 'Unknown';
    }

    /**
     * Get the ELD settings as an entity.
     */
    public function getEldSettings(): ?EldSettings
    {
        $settings = $this->get('eldSettings');

        if (empty($settings)) {
            return null;
        }

        return new EldSettings($settings);
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
     * Get the static assigned vehicle as an entity.
     */
    public function getStaticAssignedVehicle(): ?StaticAssignedVehicle
    {
        $vehicle = $this->get('staticAssignedVehicle');

        if (empty($vehicle)) {
            return null;
        }

        return new StaticAssignedVehicle($vehicle);
    }

    /**
     * Get all tag IDs associated with this driver.
     *
     * @return array<int, string>
     */
    public function getTagIds(): array
    {
        $tags = $this->tags ?? [];

        return array_map(fn (array $tag): string => (string) $tag['id'], $tags);
    }

    /**
     * Check if the driver has a static assigned vehicle.
     */
    public function hasStaticAssignedVehicle(): bool
    {
        return ! empty($this->staticAssignedVehicle);
    }

    /**
     * Check if the driver is active.
     */
    public function isActive(): bool
    {
        return $this->driverActivationStatus === 'active';
    }

    /**
     * Check if the driver is deactivated.
     */
    public function isDeactivated(): bool
    {
        return $this->driverActivationStatus === 'deactivated';
    }

    /**
     * Check if the driver is ELD exempt.
     */
    public function isEldExempt(): bool
    {
        return $this->eldExempt === true;
    }

    /**
     * Check if personal conveyance is enabled.
     */
    public function isPersonalConveyanceEnabled(): bool
    {
        return $this->eldPcEnabled === true;
    }

    /**
     * Check if yard move is enabled.
     */
    public function isYardMoveEnabled(): bool
    {
        return $this->eldYmEnabled === true;
    }
}

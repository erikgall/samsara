<?php

namespace Samsara\Data\Driver;

use Samsara\Data\Entity;
use Samsara\Enums\DriverActivationStatus;

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
 * @property-read string|null $email Email address
 * @property-read string|null $dateOfBirth Date of birth
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
 * @property-read bool|null $eldAdverseWeatherExemptionEnabled Adverse-weather exemption enabled
 * @property-read bool|null $eldBigDayExemptionEnabled Big-day exemption enabled
 * @property-read int|null $eldDayStartHour ELD day start hour
 * @property-read bool|null $eldPcEnabled Personal conveyance enabled
 * @property-read bool|null $eldYmEnabled Yard move enabled
 * @property-read bool|null $hasDrivingFeaturesHidden Driving features hidden
 * @property-read bool|null $hasVehicleUnpinningEnabled Vehicle unpinning enabled
 * @property-read bool|null $isDeactivated Whether the driver is deactivated
 * @property-read bool|null $waitingTimeDutyStatusEnabled Waiting-time duty status enabled
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array<string, mixed>>|null $attributes Driver attributes
 * @property-read array<int, array{id: string, name?: string, parentTagId?: string}>|null $tags Associated tags
 * @property-read array{rulesets?: array<int, array{name?: string, cycle?: string}>}|null $eldSettings ELD settings
 * @property-read array{carrierName?: string, dotNumber?: int, homeTerminalAddress?: string, homeTerminalName?: string, mainOfficeAddress?: string}|null $carrierSettings Carrier settings
 * @property-read array{id: string, name?: string}|null $staticAssignedVehicle Static assigned vehicle
 * @property-read array{id?: string, name?: string}|null $peerGroupTag Peer group tag
 * @property-read array{id?: string, name?: string}|null $trailerGroupTag Trailer group tag
 * @property-read array{id?: string, name?: string}|null $vehicleGroupTag Vehicle group tag
 * @property-read array{cycle: string, restart: string, restbreak: string, usStateToOverride: string}|null $usDriverRulesetOverride US driver ruleset override
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

        if (! is_array($settings) || $settings === []) {
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

        if (! is_array($settings) || $settings === []) {
            return null;
        }

        return new EldSettings($settings);
    }

    /**
     * Get an external ID by key.
     */
    public function getExternalId(string $key): ?string
    {
        $externalIds = $this->get('externalIds');

        if (! is_array($externalIds)) {
            return null;
        }

        $externalId = $externalIds[$key] ?? null;

        return is_string($externalId) ? $externalId : null;
    }

    /**
     * Get the static assigned vehicle as an entity.
     */
    public function getStaticAssignedVehicle(): ?StaticAssignedVehicle
    {
        $vehicle = $this->get('staticAssignedVehicle');

        if (! is_array($vehicle) || $vehicle === []) {
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
        $tags = $this->get('tags', []);

        if (! is_array($tags)) {
            return [];
        }

        $ids = [];

        foreach ($tags as $tag) {
            if (is_array($tag) && isset($tag['id'])) {
                $ids[] = (string) $tag['id'];
            }
        }

        return $ids;
    }

    /**
     * Check if the driver has a static assigned vehicle.
     */
    public function hasStaticAssignedVehicle(): bool
    {
        $vehicle = $this->get('staticAssignedVehicle');

        return is_array($vehicle) && $vehicle !== [];
    }

    /**
     * Check if the driver has a tag by ID.
     */
    public function hasTag(string $id): bool
    {
        return in_array($id, $this->getTagIds(), true);
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

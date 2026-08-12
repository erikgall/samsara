<?php

namespace Samsara\Data\Alert;

use Samsara\Data\Entity;

/**
 * AlertConfiguration entity.
 *
 * Represents an alert configuration.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Alert configuration ID
 * @property-read string|null $name Alert name
 * @property-read bool|null $isEnabled Whether the alert is enabled
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $lastModifiedAtTime Last modification time (RFC 3339)
 * @property-read array<int, array<string, mixed>>|null $actions Alert actions
 * @property-read array<string, string>|null $externalIds External identifiers
 * @property-read array<string, mixed>|null $operationalSettings Operational settings
 * @property-read array<string, mixed>|null $scope Alert scope
 * @property-read array<int, array<string, mixed>>|null $triggers Alert triggers
 * @property-read string|null $alertType Deprecated legacy payload field not returned by the current API
 */
class AlertConfiguration extends Entity
{
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
     * Check if the alert is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->get('isEnabled') === true;
    }

    /**
     * Check if the alert is a fuel level type.
     *
     * @deprecated The current API does not expose an alertType field.
     */
    public function isFuelLevelType(): bool
    {
        return $this->alertType === 'fuelLevel';
    }

    /**
     * Check if the alert is a geofence type.
     *
     * @deprecated The current API does not expose an alertType field.
     */
    public function isGeofenceType(): bool
    {
        return $this->alertType === 'geofence';
    }

    /**
     * Check if the alert is an idle type.
     *
     * @deprecated The current API does not expose an alertType field.
     */
    public function isIdleType(): bool
    {
        return $this->alertType === 'idle';
    }

    /**
     * Check if the alert is a speed type.
     *
     * @deprecated The current API does not expose an alertType field.
     */
    public function isSpeedType(): bool
    {
        return $this->alertType === 'speed';
    }
}

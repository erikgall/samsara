<?php

namespace ErikGall\Samsara\Data\Alert;

use ErikGall\Samsara\Data\Entity;

/**
 * AlertConfiguration entity.
 *
 * Represents an alert configuration.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Alert configuration ID
 * @property-read string|null $name Alert name
 * @property-read string|null $alertType Alert type (speed, geofence, idle, fuelLevel)
 * @property-read string|null $description Alert description
 * @property-read bool|null $enabled Whether the alert is enabled
 * @property-read array{email?: bool, sms?: bool}|null $notificationSettings Notification settings
 * @property-read array<int, string>|null $tagIds Tag IDs
 */
class AlertConfiguration extends Entity
{
    /**
     * Check if the alert is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->get('enabled') === true;
    }

    /**
     * Check if the alert is a fuel level type.
     */
    public function isFuelLevelType(): bool
    {
        return $this->alertType === 'fuelLevel';
    }

    /**
     * Check if the alert is a geofence type.
     */
    public function isGeofenceType(): bool
    {
        return $this->alertType === 'geofence';
    }

    /**
     * Check if the alert is an idle type.
     */
    public function isIdleType(): bool
    {
        return $this->alertType === 'idle';
    }

    /**
     * Check if the alert is a speed type.
     */
    public function isSpeedType(): bool
    {
        return $this->alertType === 'speed';
    }
}

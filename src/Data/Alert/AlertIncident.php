<?php

namespace Samsara\Data\Alert;

use Samsara\Data\Entity;

/**
 * AlertIncident entity.
 *
 * Represents an alert incident.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Alert incident ID
 * @property-read string|null $alertConfigurationId Alert configuration ID
 * @property-read string|null $status Incident status (triggered, resolved, dismissed)
 * @property-read string|null $triggeredAtTime Trigger time (RFC 3339)
 * @property-read string|null $resolvedAtTime Resolution time (RFC 3339)
 * @property-read array{id?: string, name?: string}|null $driver Associated driver
 * @property-read array{id?: string, name?: string}|null $vehicle Associated vehicle
 * @property-read array{latitude?: float, longitude?: float}|null $location Incident location
 */
class AlertIncident extends Entity
{
    /**
     * Check if the incident is dismissed.
     */
    public function isDismissed(): bool
    {
        return $this->status === 'dismissed';
    }

    /**
     * Check if the incident is resolved.
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if the incident is triggered.
     */
    public function isTriggered(): bool
    {
        return $this->status === 'triggered';
    }
}

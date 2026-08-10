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
 * @property-read string|null $configurationId Alert configuration ID
 * @property-read bool|null $isResolved Whether the incident is resolved
 * @property-read string|null $happenedAtTime Incident time (RFC 3339)
 * @property-read string|null $resolvedAtTime Resolution time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 * @property-read string|null $incidentUrl Cloud dashboard URL
 * @property-read array<int, array<string, mixed>>|null $conditions Incident conditions
 * @property-read string|null $status Deprecated legacy payload field not returned by the current API
 */
class AlertIncident extends Entity
{
    /**
     * Check if the incident is dismissed.
     *
     * @deprecated The current API does not expose a dismissed status.
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
        return $this->get('isResolved') === true;
    }

    /**
     * Check if the incident is triggered.
     *
     * @deprecated The current API does not expose a triggered status.
     */
    public function isTriggered(): bool
    {
        return $this->status === 'triggered';
    }
}

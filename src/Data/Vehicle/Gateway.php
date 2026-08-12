<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;

/**
 * Gateway entity.
 *
 * Represents a Samsara gateway device attached to a vehicle.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $serial Gateway serial number
 * @property-read string|null $model Gateway model
 * @property-read array<int, array<string, mixed>>|null $accessoryDevices Accessory devices
 * @property-read array<string, mixed>|null $asset Attached asset
 * @property-read array<string, mixed>|null $connectionStatus Connection status
 * @property-read array<string, mixed>|null $dataUsageLast30Days Recent data usage
 */
class Gateway extends Entity
{
    /**
     * Check whether the gateway's latest health status is connected.
     */
    public function isConnected(): bool
    {
        $connectionStatus = $this->get('connectionStatus');

        return is_array($connectionStatus)
            && ($connectionStatus['healthStatus'] ?? null) === 'Connected';
    }
}

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
class Gateway extends Entity {}

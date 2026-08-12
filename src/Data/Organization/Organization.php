<?php

namespace Samsara\Data\Organization;

use Samsara\Data\Entity;

/**
 * Organization entity.
 *
 * Represents an organization.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Organization ID
 * @property-read string|null $name Organization name
 * @property-read array{carrierName?: string, dotNumber?: int, mainOfficeAddress?: string}|null $carrierSettings Carrier settings
 */
class Organization extends Entity {}

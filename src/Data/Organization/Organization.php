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
 * @property-read string|null $address Organization address
 * @property-read array{dotNumber?: string, mcNumber?: string}|null $carrierSettings Carrier settings
 * @property-read array{timezone?: string}|null $settings Organization settings
 */
class Organization extends Entity {}

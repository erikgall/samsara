<?php

namespace Samsara\Data\Maintenance;

use Samsara\Data\Entity;

/**
 * ServiceTask entity.
 *
 * Represents a reusable maintenance service task.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Service task ID
 * @property-read string|null $name Service task name
 * @property-read string|null $description Task description
 * @property-read float|null $estimatedLaborHours Estimated labor hours
 * @property-read float|null $estimatedPartsCostUsd Estimated parts cost in USD
 * @property-read float|null $actualLaborHours Actual labor hours
 * @property-read float|null $actualPartsCostUsd Actual parts cost in USD
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 */
class ServiceTask extends Entity {}

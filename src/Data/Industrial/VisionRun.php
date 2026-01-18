<?php

namespace Samsara\Data\Industrial;

use Samsara\Data\Entity;

/**
 * VisionRun entity.
 *
 * Represents a vision program run.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Run ID
 * @property-read string|null $startTime Start time (RFC 3339)
 * @property-read string|null $endTime End time (RFC 3339)
 * @property-read string|null $programId Program ID
 */
class VisionRun extends Entity {}

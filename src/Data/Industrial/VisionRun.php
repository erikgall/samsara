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
 * The legacy vision endpoints return several incompatible run variants, so this
 * open entity documents their common scalar fields without inferring state.
 *
 * @property-read int|null $cameraId Camera ID for camera-specific responses
 * @property-read int|null $deviceId Device ID for organization-wide responses
 * @property-read int|null $startedAtMs Start time in Unix milliseconds
 * @property-read int|null $endedAtMs End time in Unix milliseconds
 * @property-read int|null $programId Program ID
 * @property-read array<string, mixed>|null $program Program details
 * @property-read array<string, mixed>|null $runSummary Run summary
 * @property-read array<int, array<string, mixed>>|null $inspectionResults Inspection results
 */
class VisionRun extends Entity {}

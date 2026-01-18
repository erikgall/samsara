<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;

/**
 * HosDailyLog entity.
 *
 * Represents an HOS daily log.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $startTime Start time of the daily log (RFC 3339)
 * @property-read string|null $endTime End time of the daily log (RFC 3339)
 * @property-read array{id?: string, name?: string}|null $driver Driver information
 * @property-read array{distanceMeters?: int}|null $distanceTraveled Distance traveled information
 * @property-read array{driveDurationMs?: int, onDutyDurationMs?: int, offDutyDurationMs?: int, sleeperBerthDurationMs?: int}|null $dutyStatusDurations Duty status durations
 * @property-read array{driveDurationMs?: int, onDutyDurationMs?: int}|null $pendingDutyStatusDurations Pending duty status durations
 * @property-read array{shippingId?: string}|null $logMetaData Log metadata
 */
class HosDailyLog extends Entity {}

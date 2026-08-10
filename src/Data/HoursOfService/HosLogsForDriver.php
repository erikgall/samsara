<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * HOS log entries grouped by driver.
 *
 * @property-read array{id?: string, name?: string}|null $driver
 * @property-read array<int, array<string, mixed>>|null $hosLogs
 */
class HosLogsForDriver extends Entity
{
    /**
     * Get the driver's typed HOS log entries.
     *
     * @return EntityCollection<int, HosLog>
     */
    public function getLogs(): EntityCollection
    {
        return new EntityCollection(array_map(
            fn (array $log): HosLog => HosLog::make($log),
            $this->get('hosLogs', [])
        ));
    }
}

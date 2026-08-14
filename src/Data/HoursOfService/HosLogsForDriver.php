<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * HOS log entries grouped by driver.
 *
 * @property-read array{id?: string, name?: string}|null $driver
 * @property-read array<int, array<string, mixed>>|null $hosLogs
 *
 * @author Erik Galloway <erik@erikgall.com>
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
        $logs = $this->get('hosLogs', []);

        if (! is_array($logs)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $log): HosLog => HosLog::make($log),
            array_values(array_filter($logs, 'is_array'))
        ));
    }
}

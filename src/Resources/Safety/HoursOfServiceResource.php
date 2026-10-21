<?php

namespace Samsara\Resources\Safety;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\HoursOfService\HosDailyLog;
use Samsara\Data\HoursOfService\HosLogsForDriver;
use Samsara\Data\HoursOfService\HosViolationGroup;
use Samsara\Data\HoursOfService\HosClocksForDriver;

/**
 * Hours of Service resource for the Samsara API.
 *
 * Provides access to HOS logs, clocks, violations, and daily logs endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<HosLogsForDriver>
 */
class HoursOfServiceResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/hos/logs';

    /**
     * The entity class for this resource.
     *
     * @var class-string<HosLogsForDriver>
     */
    protected string $entity = HosLogsForDriver::class;

    /**
     * Get a query builder for HOS clocks.
     *
     * @return Builder<HosClocksForDriver>
     */
    public function clocks(): Builder
    {
        return $this->newQuery('/fleet/hos/clocks')
            ->usingEntity(HosClocksForDriver::class);
    }

    /**
     * Get a query builder for HOS daily logs.
     *
     * @return Builder<HosDailyLog>
     */
    public function dailyLogs(): Builder
    {
        return $this->newQuery('/fleet/hos/daily-logs')
            ->withoutDefaultPageSize()
            ->usingEntity(HosDailyLog::class);
    }

    /**
     * Get a query builder for HOS logs.
     *
     * @return Builder<HosLogsForDriver>
     */
    public function logs(): Builder
    {
        return $this->query();
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<HosLogsForDriver>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize();
    }

    /**
     * Get a query builder for HOS violations.
     *
     * @return Builder<HosViolationGroup>
     */
    public function violations(): Builder
    {
        return $this->newQuery('/fleet/hos/violations')
            ->withoutDefaultPageSize()
            ->usingEntity(HosViolationGroup::class);
    }
}

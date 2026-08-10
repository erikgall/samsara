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
     */
    public function clocks(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/hos/clocks')
            ->usingEntity(HosClocksForDriver::class);
    }

    /**
     * Get a query builder for HOS daily logs.
     */
    public function dailyLogs(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/hos/daily-logs')
            ->withoutDefaultPageSize()
            ->usingEntity(HosDailyLog::class);
    }

    /**
     * Get a query builder for HOS logs.
     */
    public function logs(): Builder
    {
        return $this->query();
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return (new Builder($this))
            ->withoutDefaultPageSize()
            ->usingEntity(HosLogsForDriver::class);
    }

    /**
     * Get a query builder for HOS violations.
     */
    public function violations(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/hos/violations')
            ->withoutDefaultPageSize()
            ->usingEntity(HosViolationGroup::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['all'];
    }
}

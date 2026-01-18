<?php

namespace ErikGall\Samsara\Resources\Safety;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\HoursOfService\HosLog;

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
     * @var class-string<HosLog>
     */
    protected string $entity = HosLog::class;

    /**
     * Get a query builder for HOS clocks.
     */
    public function clocks(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/hos/clocks');
    }

    /**
     * Get a query builder for HOS daily logs.
     */
    public function dailyLogs(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/hos/daily-logs');
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
        return new Builder($this);
    }

    /**
     * Get a query builder for HOS violations.
     */
    public function violations(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/hos/violations');
    }

    /**
     * Create a builder with a custom endpoint.
     */
    protected function createBuilderWithEndpoint(string $endpoint): Builder
    {
        $originalEndpoint = $this->endpoint;
        $this->endpoint = $endpoint;
        $builder = new Builder($this);
        $this->endpoint = $originalEndpoint;

        return $builder;
    }
}

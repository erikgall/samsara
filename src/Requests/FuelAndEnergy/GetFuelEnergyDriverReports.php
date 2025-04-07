<?php

namespace ErikGall\Samsara\Requests\FuelAndEnergy;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getFuelEnergyDriverReports.
 *
 * Get fuel and energy efficiency driver reports for the requested time range.
 *
 *  <b>Rate limit:</b> 5
 * requests/sec (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read Fuel & Energy** under the Fuel &
 * Energy category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetFuelEnergyDriverReports extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startDate  A start date in RFC 3339 format. This parameter ignores everything (i.e. hour, minutes, seconds, nanoseconds, etc.) besides the date and timezone. If no time zone is passed in, then the UTC time zone will be used. This parameter is inclusive, so data on the date specified will be considered. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. For example, 2022-07-13T14:20:50.52-07:00 is a time in Pacific Daylight Time.
     * @param  string  $endDate  An end date in RFC 3339 format. This parameter ignores everything (i.e. hour, minutes, seconds, nanoseconds, etc.) besides the date and timezone. If no time zone is passed in, then the UTC time zone will be used. This parameter is inclusive, so data on the date specified will be considered. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. For example, 2022-07-13T14:20:50.52-07:00 is a time in Pacific Daylight Time.
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     */
    public function __construct(
        protected string $startDate,
        protected string $endDate,
        protected ?array $driverIds = null,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startDate'    => $this->startDate,
            'endDate'      => $this->endDate,
            'driverIds'    => $this->driverIds,
            'tagIds'       => $this->tagIds,
            'parentTagIds' => $this->parentTagIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/reports/drivers/fuel-energy';
    }
}

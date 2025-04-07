<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDriverEfficiency.
 *
 * Get all driver and associated vehicle efficiency data.
 *
 *  <b>Rate limit:</b> 50 requests/sec (learn
 * more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests should
 * be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support
 * team.
 *
 * To use this endpoint, select **Read Fuel & Energy** under the Fuel & Energy category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetDriverEfficiency extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string|null  $driverActivationStatus  If value is `deactivated`, only drivers that are deactivated will appear in the response. This parameter will default to `active` if not provided (fetching only active drivers).
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs. Cannot be used with tag filtering or driver status. Example: `driverIds=1234,5678`
     * @param  array|null  $driverTagIds  Filters summary to drivers based on this comma-separated list of tag IDs. Data from all the drivers' respective vehicles will be included in the summary, regardless of which tag the vehicle is associated with. Should not be provided in addition to `driverIds`. Example: driverTagIds=1234,5678
     * @param  array|null  $driverParentTagIds  Filters like `driverTagIds` but includes descendants of all the given parent tags. Should not be provided in addition to `driverIds`. Example: `driverParentTagIds=1234,5678`
     * @param  string|null  $startTime  A start time in RFC 3339 format. The results will be truncated to the hour mark for the provided time. For example, if `startTime` is 2020-03-17T12:06:19Z then the results will include data starting from 2020-03-17T12:00:00Z. The provided start time cannot be in the future. Start time can be at most 31 days before the end time. If the start time is within the last hour, the results will be empty. Default: 24 hours prior to endTime.
     *
     * Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours.
     * @param  string|null  $endTime  An end time in RFC 3339 format. The results will be truncated to the hour mark for the provided time. For example, if `endTime` is 2020-03-17T12:06:19Z then the results will include data up until 2020-03-17T12:00:00Z. The provided end time cannot be in the future. End time can be at most 31 days after the start time. Default: The current time truncated to the hour mark.
     *
     * Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours
     */
    public function __construct(
        protected ?string $driverActivationStatus = null,
        protected ?array $driverIds = null,
        protected ?array $driverTagIds = null,
        protected ?array $driverParentTagIds = null,
        protected ?string $startTime = null,
        protected ?string $endTime = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'driverActivationStatus' => $this->driverActivationStatus,
            'driverIds'              => $this->driverIds,
            'driverTagIds'           => $this->driverTagIds,
            'driverParentTagIds'     => $this->driverParentTagIds,
            'startTime'              => $this->startTime,
            'endTime'                => $this->endTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/beta/fleet/drivers/efficiency';
    }
}

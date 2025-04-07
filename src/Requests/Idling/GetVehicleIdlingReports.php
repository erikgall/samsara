<?php

namespace ErikGall\Samsara\Requests\Idling;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getVehicleIdlingReports.
 *
 * Get all vehicle idling reports for the requested time duration.
 *
 *  <b>Rate limit:</b> 25 requests/sec
 * (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
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
class GetVehicleIdlingReports extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. Note that the most recent 72 hours of data may still be processing and is subject to change and latency, so it is not recommended to request data for the most recent 72 hours. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  bool|null  $isPtoActive  A filter on the data based on power take-off being active or inactive.
     * @param  int|null  $minIdlingDurationMinutes  A filter on the data based on a minimum idling duration.
     */
    public function __construct(
        protected ?int $limit,
        protected string $startTime,
        protected string $endTime,
        protected ?string $vehicleIds = null,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
        protected ?bool $isPtoActive = null,
        protected ?int $minIdlingDurationMinutes = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit'                    => $this->limit,
            'startTime'                => $this->startTime,
            'endTime'                  => $this->endTime,
            'vehicleIds'               => $this->vehicleIds,
            'tagIds'                   => $this->tagIds,
            'parentTagIds'             => $this->parentTagIds,
            'isPtoActive'              => $this->isPtoActive,
            'minIdlingDurationMinutes' => $this->minIdlingDurationMinutes,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/reports/vehicle/idling';
    }
}

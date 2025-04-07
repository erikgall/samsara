<?php

namespace ErikGall\Samsara\Requests\SpeedingIntervals;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getSpeedingIntervals.
 *
 * This endpoint will return all speeding intervals associated with trips that have been collected for
 * your organization based on the time parameters passed in. Only completed trips are included. Results
 * are paginated.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Speeding Intervals** under the Speeding Intervals category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetSpeedingIntervals extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array  $assetIds  Comma-separated list of asset IDs. Include up to 50 asset IDs.
     * @param  string  $startTime  RFC 3339 timestamp that indicates when to begin receiving data. Value is compared against `updatedAtTime` or `tripStartTime` depending on the queryBy parameter.
     * @param  string|null  $endTime  RFC 3339 timestamp which is compared against `updatedAtTime` or `tripStartTime` depending on the queryBy parameter. If not provided then the endpoint behaves as an unending feed of changes.
     * @param  string|null  $queryBy  Decide which timestamp the `startTime` and `endTime` are compared to.  Valid values: `updatedAtTime`, `tripStartTime`
     * @param  bool|null  $includeAsset  Indicates whether or not to return expanded “asset” data
     * @param  bool|null  $includeDriverId  Indicates whether or not to return trip's driver id
     * @param  array|null  $severityLevels  Optional string of comma-separated severity levels to filter speeding intervals by. Valid values:  “light”, ”moderate”, ”heavy”, “severe”.
     */
    public function __construct(
        protected array $assetIds,
        protected string $startTime,
        protected ?string $endTime = null,
        protected ?string $queryBy = null,
        protected ?bool $includeAsset = null,
        protected ?bool $includeDriverId = null,
        protected ?array $severityLevels = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'assetIds'        => $this->assetIds,
            'startTime'       => $this->startTime,
            'endTime'         => $this->endTime,
            'queryBy'         => $this->queryBy,
            'includeAsset'    => $this->includeAsset,
            'includeDriverId' => $this->includeDriverId,
            'severityLevels'  => $this->severityLevels,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/speeding-intervals/stream';
    }
}

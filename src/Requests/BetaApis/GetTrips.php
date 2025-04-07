<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getTrips.
 *
 * This endpoint will return trips that have been collected for your organization based on the time
 * parameters passed in. Results are paginated. Reach out to your Samsara Representative to have this
 * API enabled for your organization.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits
 * <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Trips** under the Trips category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetTrips extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  bool|null  $includeAsset  Indicates whether or not to return expanded “asset” data
     * @param  string|null  $completionStatus  Filters trips based on a specific completion status  Valid values: `inProgress`, `completed`, `all`
     * @param  string  $startTime  RFC 3339 timestamp that indicates when to begin receiving data. Value is compared against `updatedAtTime` or `tripStartTime` depending on the queryBy parameter.
     * @param  string|null  $endTime  RFC 3339 timestamp which is compared against `updatedAtTime` or `tripStartTime` depending on the queryBy parameter. If not provided then the endpoint behaves as an unending feed of changes.
     * @param  string|null  $queryBy  Decide which timestamp the `startTime` and `endTime` are compared to.  Valid values: `updatedAtTime`, `tripStartTime`
     * @param  array  $ids  Comma-separated list of asset IDs. Include up to 50 asset IDs.
     */
    public function __construct(
        protected ?bool $includeAsset,
        protected ?string $completionStatus,
        protected string $startTime,
        protected ?string $endTime,
        protected ?string $queryBy,
        protected array $ids,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'includeAsset'     => $this->includeAsset,
            'completionStatus' => $this->completionStatus,
            'startTime'        => $this->startTime,
            'endTime'          => $this->endTime,
            'queryBy'          => $this->queryBy,
            'ids'              => $this->ids,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/trips/stream';
    }
}

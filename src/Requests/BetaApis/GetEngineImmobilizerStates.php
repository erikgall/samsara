<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getEngineImmobilizerStates.
 *
 * Get the engine immobilizer states of the queried vehicles. If a vehicle has never had an engine
 * immobilizer connected, there won't be any state returned for that vehicle.
 *
 *  <b>Rate limit:</b> 5
 * requests/sec (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read Vehicle Immobilization** under the
 * Vehicles category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetEngineImmobilizerStates extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function __construct(
        protected string $vehicleIds,
        protected string $startTime,
        protected ?string $endTime = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'vehicleIds' => $this->vehicleIds,
            'startTime'  => $this->startTime,
            'endTime'    => $this->endTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/vehicles/immobilizer/stream';
    }
}

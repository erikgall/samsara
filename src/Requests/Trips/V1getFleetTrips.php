<?php

namespace ErikGall\Samsara\Requests\Trips;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getFleetTrips.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Get historical trips data for specified vehicle. This method returns a set
 * of historical trips data for the specified vehicle in the specified time range.
 *
 *  **Submit
 * Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Vehicle Trips** under the Vehicles category when creating or editing an
 * API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getFleetTrips extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $vehicleId  Vehicle ID to query.
     * @param  int  $startMs  Beginning of the time range, specified in milliseconds UNIX time. Limited to a 90 day window with respect to startMs and endMs
     * @param  int  $endMs  End of the time range, specified in milliseconds UNIX time.
     */
    public function __construct(
        protected int $vehicleId,
        protected int $startMs,
        protected int $endMs,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['vehicleId' => $this->vehicleId, 'startMs' => $this->startMs, 'endMs' => $this->endMs]);
    }

    public function resolveEndpoint(): string
    {
        return '/v1/fleet/trips';
    }
}

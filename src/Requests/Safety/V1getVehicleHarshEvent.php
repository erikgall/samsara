<?php

namespace ErikGall\Samsara\Requests\Safety;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getVehicleHarshEvent.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Fetch harsh event details for a vehicle.
 *
 *  **Submit Feedback**: Likes,
 * dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Safety Events & Scores** under the Safety & Cameras category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getVehicleHarshEvent extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $vehicleId  ID of the vehicle. Must contain only digits 0-9.
     * @param  int  $timestamp  Timestamp in milliseconds representing the timestamp of a harsh event.
     */
    public function __construct(protected int $vehicleId, protected int $timestamp) {}

    public function defaultQuery(): array
    {
        return array_filter(['timestamp' => $this->timestamp]);
    }

    public function resolveEndpoint(): string
    {
        return "/v1/fleet/vehicles/{$this->vehicleId}/safety/harsh_event";
    }
}

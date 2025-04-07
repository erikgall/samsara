<?php

namespace ErikGall\Samsara\Requests\HoursOfService;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getFleetHosAuthenticationLogs.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Get the HOS (hours of service) signin and signout logs for the specified
 * driver. The response includes 4 fields that are now deprecated.
 *
 * **Note:** If data is still being
 * uploaded from the Samsara Driver App, it may not be completely reflected in the response from this
 * endpoint. The best practice is to wait a couple of days before querying this endpoint to make sure
 * that all data from the Samsara Driver App has been uploaded.
 *
 *  **Submit Feedback**: Likes,
 * dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read ELD Hours of Service (US)** under the Compliance category when creating
 * or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getFleetHosAuthenticationLogs extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $driverId  Driver ID to query.
     * @param  int  $startMs  Beginning of the time range, specified in milliseconds UNIX time.
     * @param  int  $endMs  End of the time range, specified in milliseconds UNIX time.
     */
    public function __construct(
        protected int $driverId,
        protected int $startMs,
        protected int $endMs
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'driverId' => $this->driverId,
            'startMs'  => $this->startMs,
            'endMs'    => $this->endMs,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/v1/fleet/hos_authentication_logs';
    }
}

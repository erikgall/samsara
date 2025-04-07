<?php

namespace ErikGall\Samsara\Requests\Drivers;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateDriver.
 *
 * Update a specific driver's information. This can also be used to activate or de-activate a given
 * driver by setting the driverActivationStatus field. If the driverActivationStatus field is
 * 'deactivated' then the user can also specify the deactivatedAtTime. The deactivatedAtTime cannot be
 * more than 6 months in the past and must not come before the dirver's latest active HOS log. It will
 * be considered an error if deactivatedAtTime is provided with a driverActivationStatus of active.
 *
 *
 * **Submit Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Write Drivers** under the Drivers category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class UpdateDriver extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  string  $id  ID of the driver. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     */
    public function __construct(protected string $id) {}

    public function resolveEndpoint(): string
    {
        return "/fleet/drivers/{$this->id}";
    }
}

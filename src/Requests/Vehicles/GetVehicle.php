<?php

namespace ErikGall\Samsara\Requests\Vehicles;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getVehicle.
 *
 * Get information about a specific vehicle.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature
 * requests should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69"
 * target="_blank">API feedback form</a>. If you encountered an issue or noticed inaccuracies in the
 * API documentation, please <a href="https://www.samsara.com/help" target="_blank">submit a case</a>
 * to our support team.
 *
 * To use this endpoint, select **Read Vehicles** under the Vehicles category
 * when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetVehicle extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $id  ID of the vehicle. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource, or automatically populated by fields on the vehicle. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `maintenanceId:250020`. Automatically populated external IDs are prefixed with `samsara.`. For example, `samsara.vin:1HGBH41JXMN109186`.
     */
    public function __construct(protected string $id) {}

    public function resolveEndpoint(): string
    {
        return "/fleet/vehicles/{$this->id}";
    }
}

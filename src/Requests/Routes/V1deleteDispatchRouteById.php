<?php

namespace ErikGall\Samsara\Requests\Routes;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1deleteDispatchRouteById.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Delete a dispatch route and its associated jobs.
 *
 *  **Submit Feedback**:
 * Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Write Routes** under the Driver Workflow category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1deleteDispatchRouteById extends Request
{
    protected Method $method = Method::DELETE;

    /**
     * @param  string  $routeIdOrExternalId  ID of the route. This can either be the Samsara-specified ID, or an external ID. External IDs are customer specified key-value pairs created in the POST or PATCH requests of this resource. To specify an external ID as part of a path parameter, use the following format: `key:value`. For example, `payrollId:ABFS18600`
     */
    public function __construct(
        protected string $routeIdOrExternalId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/v1/fleet/dispatch/routes/{$this->routeIdOrExternalId}";
    }
}

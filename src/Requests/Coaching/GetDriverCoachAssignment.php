<?php

namespace ErikGall\Samsara\Requests\Coaching;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDriverCoachAssignment.
 *
 * This endpoint will return coach assignments for your organization based on the parameters passed in.
 * Results are paginated.
 *
 *  <b>Rate limit:</b> 10 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Coaching** under the Coaching category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetDriverCoachAssignment extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $driverIds  Optional string of comma separated IDs of the drivers. This can be either a unique Samsara driver ID or an external ID for the driver.
     * @param  array|null  $coachIds  Optional string of comma separated IDs of the coaches.
     * @param  bool|null  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function __construct(
        protected ?array $driverIds = null,
        protected ?array $coachIds = null,
        protected ?bool $includeExternalIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['driverIds' => $this->driverIds, 'coachIds' => $this->coachIds, 'includeExternalIds' => $this->includeExternalIds]);
    }

    public function resolveEndpoint(): string
    {
        return '/coaching/driver-coach-assignments';
    }
}

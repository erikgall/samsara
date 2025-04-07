<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDriverTrailerAssignments.
 *
 * Get currently active driver-trailer assignments for driver.
 *
 *  <b>Rate limit:</b> 5 requests/sec
 * (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read Assignments** under the Assignments
 * category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetDriverTrailerAssignments extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  bool|null  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function __construct(
        protected array $driverIds,
        protected ?bool $includeExternalIds = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'driverIds'          => $this->driverIds,
            'includeExternalIds' => $this->includeExternalIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/driver-trailer-assignments';
    }
}

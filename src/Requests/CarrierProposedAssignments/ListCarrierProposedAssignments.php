<?php

namespace ErikGall\Samsara\Requests\CarrierProposedAssignments;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listCarrierProposedAssignments.
 *
 * Show the assignments created by the POST fleet/carrier-proposed-assignments. This endpoint will only
 * show the assignments that are active for drivers and currently visible to them in the driver app.
 * Once a proposed assignment has been accepted, the endpoint will not return any data.
 *
 *  **Submit
 * Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Carrier-Proposed Assignments** under the Assignments category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class ListCarrierProposedAssignments extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array|null  $driverIds  A filter on the data based on this comma-separated list of driver IDs and externalIds. Example: `driverIds=1234,5678,payroll:4841`
     * @param  string|null  $activeTime  If specified, shows assignments that will be active at this time. Defaults to now, which would show current active assignments. In RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function __construct(
        protected ?int $limit = null,
        protected ?array $driverIds = null,
        protected ?string $activeTime = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['limit' => $this->limit, 'driverIds' => $this->driverIds, 'activeTime' => $this->activeTime]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/carrier-proposed-assignments';
    }
}

<?php

namespace ErikGall\Samsara\Requests\DriverVehicleAssignments;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateDriverVehicleAssignment.
 *
 * Update driver assignments that were created using the `POST fleet/driver-vehicle-assignments`.
 * Vehicle Id, Driver Id, and Start Time must match an existing assignment.
 *
 *  <b>Rate limit:</b> 100
 * requests/min (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Write Assignments** under the Assignments
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
class UpdateDriverVehicleAssignment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(protected array $payload = []) {}

    public function resolveEndpoint(): string
    {
        return '/fleet/driver-vehicle-assignments';
    }

    /**
     * Default body.
     *
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}

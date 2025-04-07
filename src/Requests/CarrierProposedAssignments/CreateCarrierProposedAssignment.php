<?php

namespace ErikGall\Samsara\Requests\CarrierProposedAssignments;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * createCarrierProposedAssignment.
 *
 * Creates a new assignment that a driver can later use. Each driver can only have one future
 * assignment.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests should be filed as
 * feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>.
 * If you encountered an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Write Carrier-Proposed Assignments** under the Assignments category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class CreateCarrierProposedAssignment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct() {}

    public function resolveEndpoint(): string
    {
        return '/fleet/carrier-proposed-assignments';
    }
}

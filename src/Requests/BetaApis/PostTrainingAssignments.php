<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * postTrainingAssignments.
 *
 * Create training assignments. Existing assignments will remain unchanged.
 *
 * **Beta:** This endpoint
 * is in beta and is likely to change before being broadly available. Reach out to your Samsara
 * Representative to have Training APIs enabled for your organization.
 *
 *  <b>Rate limit:</b> 10
 * requests/sec (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Write Training Assignments** under the
 * Training Assignments category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class PostTrainingAssignments extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $courseId  String for the course ID.
     * @param  string  $dueAtTime  Due date of the training assignment in RFC 3339 format. Millisecond precision and timezones are supported.
     * @param  array  $learnerIds  Optional string of comma separated learner IDs. If learner ID is present, training assignments for the specified learner(s) will be returned. Max value for this value is 100 objects. Example: `learnerIds=driver-281474,driver-46282156`
     */
    public function __construct(
        protected string $courseId,
        protected string $dueAtTime,
        protected array $learnerIds
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'courseId'   => $this->courseId,
            'dueAtTime'  => $this->dueAtTime,
            'learnerIds' => $this->learnerIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/training-assignments';
    }
}

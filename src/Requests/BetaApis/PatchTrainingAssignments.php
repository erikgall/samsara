<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * patchTrainingAssignments.
 *
 * Update training assignments.
 *
 * **Beta:** This endpoint is in beta and is likely to change before
 * being broadly available. Reach out to your Samsara Representative to have Training APIs enabled for
 * your organization.
 *
 *  <b>Rate limit:</b> 10 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Write Training Assignments** under the Training Assignments category when
 * creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class PatchTrainingAssignments extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  array  $ids  String of comma separated assignments IDs. Max value for this value is 100 objects .Example: `ids=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     * @param  string  $dueAtTime  Due date of the training assignment in RFC 3339 format. Millisecond precision and timezones are supported.
     */
    public function __construct(
        protected array $ids,
        protected string $dueAtTime,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['ids' => $this->ids, 'dueAtTime' => $this->dueAtTime]);
    }

    public function resolveEndpoint(): string
    {
        return '/training-assignments';
    }
}

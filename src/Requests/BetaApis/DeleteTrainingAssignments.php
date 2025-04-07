<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * deleteTrainingAssignments.
 *
 * This endpoint supports batch deletion operations. The response does not indicate which specific
 * deletions, if any, have failed. On a successful deletion or partial failure, a ‘204 No Content’
 * status is returned.
 *
 * **Beta:** This endpoint is in beta and is likely to change before being
 * broadly available. Reach out to your Samsara Representative to have Training APIs enabled for your
 * organization.
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
class DeleteTrainingAssignments extends Request
{
    protected Method $method = Method::DELETE;

    /**
     * @param  array  $ids  String of comma separated assignments IDs. Max value for this value is 100 objects .Example: `ids=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     */
    public function __construct(protected array $ids) {}

    public function defaultQuery(): array
    {
        return array_filter(['ids' => $this->ids]);
    }

    public function resolveEndpoint(): string
    {
        return '/training-assignments';
    }
}

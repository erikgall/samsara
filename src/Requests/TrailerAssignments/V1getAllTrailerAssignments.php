<?php

namespace ErikGall\Samsara\Requests\TrailerAssignments;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getAllTrailerAssignments.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Fetch trailer assignment data for all trailers in your organization.
 *
 *
 * <b>Rate limit:</b> 100 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 *  **Submit
 * Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Assignments** under the Assignments category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getAllTrailerAssignments extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $startMs  Timestamp in Unix epoch miliseconds representing the start of the period to fetch. Omitting both startMs and endMs only returns current assignments.
     * @param  int|null  $endMs  Timestamp in Unix epoch miliseconds representing the end of the period to fetch. Omitting endMs sets endMs as the current time
     * @param  float|int|null  $limit  Pagination parameter indicating the number of results to return in this request. Used in conjunction with either 'startingAfter' or 'endingBefore'.
     * @param  string|null  $startingAfter  Pagination parameter indicating the cursor position to continue returning results after. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'endingBefore' parameter.
     * @param  string|null  $endingBefore  Pagination parameter indicating the cursor position to return results before. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'startingAfter' parameter.
     */
    public function __construct(
        protected ?int $startMs = null,
        protected ?int $endMs = null,
        protected float|int|null $limit = null,
        protected ?string $startingAfter = null,
        protected ?string $endingBefore = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startMs'       => $this->startMs,
            'endMs'         => $this->endMs,
            'limit'         => $this->limit,
            'startingAfter' => $this->startingAfter,
            'endingBefore'  => $this->endingBefore,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/v1/fleet/trailers/assignments';
    }
}

<?php

namespace ErikGall\Samsara\Requests\TrailerAssignments;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getFleetTrailerAssignments.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Fetch trailer assignment data for a single trailer.
 *
 *  <b>Rate limit:</b>
 * 100 requests/sec (learn more about rate limits <a
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
class V1getFleetTrailerAssignments extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $trailerId  ID of trailer. Must contain only digits 0-9.
     * @param  int|null  $startMs  Timestamp in Unix epoch milliseconds representing the start of the period to fetch. Omitting both startMs and endMs only returns current assignments.
     * @param  int|null  $endMs  Timestamp in Unix epoch milliseconds representing the end of the period to fetch. Omitting endMs sets endMs as the current time
     */
    public function __construct(
        protected int $trailerId,
        protected ?int $startMs = null,
        protected ?int $endMs = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['startMs' => $this->startMs, 'endMs' => $this->endMs]);
    }

    public function resolveEndpoint(): string
    {
        return "/v1/fleet/trailers/{$this->trailerId}/assignments";
    }
}

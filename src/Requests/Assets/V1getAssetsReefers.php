<?php

namespace ErikGall\Samsara\Requests\Assets;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getAssetsReefers.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Fetches all reefers and reefer-specific stats.
 *
 *  **Submit Feedback**:
 * Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Trailers** under the Trailers category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getAssetsReefers extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $startMs  Timestamp in milliseconds representing the start of the period to fetch, inclusive. Used in combination with endMs.
     * @param  int  $endMs  Timestamp in milliseconds representing the end of the period to fetch, inclusive. Used in combination with startMs.
     * @param  string|null  $startingAfter  Pagination parameter indicating the cursor position to continue returning results after. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'endingBefore' parameter.
     * @param  string|null  $endingBefore  Pagination parameter indicating the cursor position to return results before. Used in conjunction with the 'limit' parameter. Mutually exclusive with 'startingAfter' parameter.
     * @param  float|int|null  $limit  Pagination parameter indicating the number of results to return in this request. Used in conjunction with either 'startingAfter' or 'endingBefore'.
     */
    public function __construct(
        protected int $startMs,
        protected int $endMs,
        protected ?string $startingAfter = null,
        protected ?string $endingBefore = null,
        protected float|int|null $limit = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startMs'       => $this->startMs,
            'endMs'         => $this->endMs,
            'startingAfter' => $this->startingAfter,
            'endingBefore'  => $this->endingBefore,
            'limit'         => $this->limit,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/v1/fleet/assets/reefers';
    }
}

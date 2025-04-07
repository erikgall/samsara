<?php

namespace ErikGall\Samsara\Requests\Safety;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getDriverSafetyScore.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Fetch the safety score for the driver.
 *
 *  <b>Rate limit:</b> 5 requests/sec
 * (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests should
 * be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support
 * team.
 *
 * To use this endpoint, select **Read Safety Events & Scores** under the Safety & Cameras
 * category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getDriverSafetyScore extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $driverId  ID of the driver. Must contain only digits 0-9.
     * @param  int  $startMs  Timestamp in milliseconds representing the start of the period to fetch, inclusive. Used in combination with endMs. Total duration (endMs - startMs) must be greater than or equal to 1 hour.
     * @param  int  $endMs  Timestamp in milliseconds representing the end of the period to fetch, inclusive. Used in combination with startMs. Total duration (endMs - startMs) must be greater than or equal to 1 hour.
     */
    public function __construct(
        protected int $driverId,
        protected int $startMs,
        protected int $endMs,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['startMs' => $this->startMs, 'endMs' => $this->endMs]);
    }

    public function resolveEndpoint(): string
    {
        return "/v1/fleet/drivers/{$this->driverId}/safety/score";
    }
}

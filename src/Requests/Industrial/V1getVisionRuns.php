<?php

namespace ErikGall\Samsara\Requests\Industrial;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getVisionRuns.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Fetch runs.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature
 * requests should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69"
 * target="_blank">API feedback form</a>. If you encountered an issue or noticed inaccuracies in the
 * API documentation, please <a href="https://www.samsara.com/help" target="_blank">submit a case</a>
 * to our support team.
 *
 * To use this endpoint, select **Read Industrial** under the Industrial category
 * when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getVisionRuns extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $durationMs  DurationMs is a required param. This works with the EndMs parameter. Indicates the duration in which the visionRuns will be fetched
     * @param  int|null  $endMs  EndMs is an optional param. It will default to the current time.
     */
    public function __construct(protected int $durationMs, protected ?int $endMs = null) {}

    public function defaultQuery(): array
    {
        return array_filter(['durationMs' => $this->durationMs, 'endMs' => $this->endMs]);
    }

    public function resolveEndpoint(): string
    {
        return '/v1/industrial/vision/runs';
    }
}

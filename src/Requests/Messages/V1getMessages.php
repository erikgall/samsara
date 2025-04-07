<?php

namespace ErikGall\Samsara\Requests\Messages;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getMessages.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Get all messages.
 *
 *  <b>Rate limit:</b> 75 requests/sec (learn more about
 * rate limits <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 *
 * **Submit Feedback**: Likes, dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Messages** under the Driver Workflow category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getMessages extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $endMs  Time in unix milliseconds that represents the end of time range of messages to return. Used in combination with durationMs. Defaults to now.
     * @param  int|null  $durationMs  Time in milliseconds that represents the duration before endMs to query. Defaults to 24 hours.
     */
    public function __construct(protected ?int $endMs = null, protected ?int $durationMs = null) {}

    public function defaultQuery(): array
    {
        return array_filter(['endMs' => $this->endMs, 'durationMs' => $this->durationMs]);
    }

    public function resolveEndpoint(): string
    {
        return '/v1/fleet/messages';
    }
}

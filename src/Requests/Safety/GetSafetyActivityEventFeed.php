<?php

namespace ErikGall\Samsara\Requests\Safety;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getSafetyActivityEventFeed.
 *
 * Get continuous safety events. The safety activity event feed offers a change-log for safety events.
 * Use this endpoint to subscribe to safety event changes. See documentation below for all supported
 * change-log types.
 *
 * | ActivityType      | Description |
 * | ----------- | ----------- |
 * |
 * CreateSafetyEventActivityType | a new safety event is processed by Samsara      |
 * |
 * BehaviorLabelActivityType     | a label is added or removed from a safety event |
 * |
 * CoachingStateActivityType     | a safety event coaching state is updated        |
 *
 *  <b>Rate
 * limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Safety Events & Scores** under the Safety & Cameras category when creating
 * or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetSafetyActivityEventFeed extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string|null  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function __construct(protected ?string $startTime = null) {}

    public function defaultQuery(): array
    {
        return array_filter(['startTime' => $this->startTime]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/safety-events/audit-logs/feed';
    }
}

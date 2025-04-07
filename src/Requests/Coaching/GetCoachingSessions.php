<?php

namespace ErikGall\Samsara\Requests\Coaching;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getCoachingSessions.
 *
 * This endpoint will return coaching sessions for your organization based on the time parameters
 * passed in. Results are paginated by sessions. If you include an endTime, the endpoint will return
 * data up until that point. If you don’t include an endTime, you can continue to poll the API
 * real-time with the pagination cursor that gets returned on every call.
 *
 *  <b>Rate limit:</b> 5
 * requests/sec (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read Coaching** under the Coaching
 * category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetCoachingSessions extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $driverIds  Optional string of comma separated driver IDs. If driver ID is present, sessions for the specified driver(s) will be returned.
     * @param  array|null  $coachIds  Optional string of comma separated user IDs. If coach ID is present, sessions for the specified coach(s) will be returned for either assignedCoach or completedCoach. If both driverId(s) and coachId(s) are present, sessions with specified driver(s) and coach(es) will be returned.
     * @param  array|null  $sessionStatuses  Optional string of comma separated statuses. Valid values:  “upcoming”, “completed”, “deleted”.
     * @param  bool|null  $includeCoachableEvents  Optional boolean to control whether behaviors will include coachableEvents in the response. Defaults to false.
     * @param  string  $startTime  Required RFC 3339 timestamp that indicates when to begin receiving data. Value is compared against `updatedAtTime`
     * @param  string|null  $endTime  Optional RFC 3339 timestamp. If not provided then the endpoint behaves as an unending feed of changes. If endTime is set the same as startTime, the most recent data point before that time will be returned per asset. Value is compared against `updatedAtTime`
     * @param  bool|null  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function __construct(
        protected ?array $driverIds,
        protected ?array $coachIds,
        protected ?array $sessionStatuses,
        protected ?bool $includeCoachableEvents,
        protected string $startTime,
        protected ?string $endTime = null,
        protected ?bool $includeExternalIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'driverIds'              => $this->driverIds,
            'coachIds'               => $this->coachIds,
            'sessionStatuses'        => $this->sessionStatuses,
            'includeCoachableEvents' => $this->includeCoachableEvents,
            'startTime'              => $this->startTime,
            'endTime'                => $this->endTime,
            'includeExternalIds'     => $this->includeExternalIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/coaching/sessions/stream';
    }
}

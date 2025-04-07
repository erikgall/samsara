<?php

namespace ErikGall\Samsara\Requests\Alerts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getIncidents.
 *
 * Get Alert Incidents for specific Alert Configurations over a specified period of time.
 *
 *  <b>Rate
 * limit:</b> 10 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Alerts** under the Alerts category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetIncidents extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  Required RFC 3339 timestamp that indicates when to begin receiving data. This will be based on updatedAtTime.
     * @param  array  $configurationIds  Required array of alert configuration ids to return incident data for.
     * @param  string|null  $endTime  Optional RFC 3339 timestamp to stop receiving data. Defaults to now if not provided. This will be based on updatedAtTime.
     */
    public function __construct(
        protected string $startTime,
        protected array $configurationIds,
        protected ?string $endTime = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'        => $this->startTime,
            'configurationIds' => $this->configurationIds,
            'endTime'          => $this->endTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/alerts/incidents/stream';
    }
}

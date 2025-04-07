<?php

namespace ErikGall\Samsara\Requests\Maintenance;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * streamDefects.
 *
 * Stream DVIR defects.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Defects** under the Maintenance category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class StreamDefects extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 200 objects.
     * @param  string  $startTime  Required RFC 3339 timestamp to begin the feed or history by `updatedAtTime` at `startTime`.
     * @param  string|null  $endTime  Optional RFC 3339 timestamp. If not provided then the endpoint behaves as an unending feed of changes.
     * @param  bool|null  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     * @param  bool|null  $isResolved  Boolean value for whether filter defects by resolved status.
     */
    public function __construct(
        protected ?int $limit,
        protected string $startTime,
        protected ?string $endTime = null,
        protected ?bool $includeExternalIds = null,
        protected ?bool $isResolved = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit'              => $this->limit,
            'startTime'          => $this->startTime,
            'endTime'            => $this->endTime,
            'includeExternalIds' => $this->includeExternalIds,
            'isResolved'         => $this->isResolved,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/defects/stream';
    }
}

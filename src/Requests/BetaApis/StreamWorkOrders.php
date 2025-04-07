<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * streamWorkOrders.
 *
 * Stream work orders.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Work Orders** under the Closed Beta category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class StreamWorkOrders extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $workOrderStatuses  Work Order status filter.
     * @param  array|null  $assetIds  Work Order asset id filter. Up to 50 ids.
     * @param  array|null  $assignedUserIds  Work Order assigned user id filter. Up to 50 ids.
     */
    public function __construct(
        protected string $startTime,
        protected ?string $endTime = null,
        protected ?array $workOrderStatuses = null,
        protected ?array $assetIds = null,
        protected ?array $assignedUserIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'         => $this->startTime,
            'endTime'           => $this->endTime,
            'workOrderStatuses' => $this->workOrderStatuses,
            'assetIds'          => $this->assetIds,
            'assignedUserIds'   => $this->assignedUserIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/maintenance/work-orders/stream';
    }
}

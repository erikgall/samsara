<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getJobs.
 *
 * Fetches jobs based on id/uuid or provided filters.
 *
 * To use this endpoint, select **Read Jobs** under
 * the Equipment category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetJobs extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string|null  $id  A jobId or uuid in STRING format. JobId must be prefixed with `jobId:`(Examples: `"8d218e6c-7a16-4f9f-90f7-cc1d93b9e596"`, `"jobId:98765"`).
     * @param  string|null  $startDate  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endDate  An end time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $industrialAssetIds  IndustrialAssetId in STRING format. (Example: `"8d218e6c-7a16-4f9f-90f7-cc1d93b9e596"`).
     * @param  array|null  $fleetDeviceIds  FleetDeviceId in INTEGER format. (Example: `123456`).
     * @param  string|null  $status  A job status in STRING format. Job statuses can be one of three (ignores case): `"active", "scheduled", "completed"`  Valid values: `active`, `scheduled`, `completed`
     * @param  string|null  $customerName  Customer name to filter by
     */
    public function __construct(
        protected ?string $id = null,
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?array $industrialAssetIds = null,
        protected ?array $fleetDeviceIds = null,
        protected ?string $status = null,
        protected ?string $customerName = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'id'                 => $this->id,
            'startDate'          => $this->startDate,
            'endDate'            => $this->endDate,
            'industrialAssetIds' => $this->industrialAssetIds,
            'fleetDeviceIds'     => $this->fleetDeviceIds,
            'status'             => $this->status,
            'customerName'       => $this->customerName,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/beta/industrial/jobs';
    }
}

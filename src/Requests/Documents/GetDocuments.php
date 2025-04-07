<?php

namespace ErikGall\Samsara\Requests\Documents;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDocuments.
 *
 * Get all documents for the given time range. The legacy version of this endpoint can be found at
 * [samsara.com/api-legacy](https://www.samsara.com/api-legacy#operation/getDriverDocumentsByOrgId).
 *
 *
 * <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Documents** under the Driver Workflow category when creating or editing an
 * API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetDocuments extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $documentTypeId  ID of the document template type.
     * @param  string|null  $queryBy  Query by document creation time (`created`) or updated time (`updated`). Defaults to `created`.
     */
    public function __construct(
        protected string $startTime,
        protected string $endTime,
        protected ?string $documentTypeId = null,
        protected ?string $queryBy = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'      => $this->startTime,
            'endTime'        => $this->endTime,
            'documentTypeId' => $this->documentTypeId,
            'queryBy'        => $this->queryBy,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/documents';
    }
}

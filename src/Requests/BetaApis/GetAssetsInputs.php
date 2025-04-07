<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getAssetsInputs.
 *
 * This endpoint will return data collected from the inputs of your organization's assets based on the
 * time parameters passed in. Results are paginated. If you include an endTime, the endpoint will
 * return data up until that point. If you don’t include an endTime, you can continue to poll the API
 * real-time with the pagination cursor that gets returned on every call. The endpoint will only return
 * data up until the endTime that has been processed by the server at the time of the original request.
 * You will need to request the same [startTime, endTime) range again to receive data for assets
 * processed after the original request time. This endpoint sorts data by time ascending.
 *
 *  <b>Rate
 * limit:</b> 10 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Assets** under the Assets category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetAssetsInputs extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array  $ids  Comma-separated list of asset IDs. Limited to 100 ID's for each request.
     * @param  string  $type  Input stat type to query for.  Valid values: `auxInput1`, `auxInput2`, `auxInput3`, `auxInput4`, `auxInput5`, `auxInput6`, `auxInput7`, `auxInput8`, `auxInput9`, `auxInput10`, `auxInput11`, `auxInput12`, `auxInput13`, `analogInput1Voltage`, `analogInput2Voltage`, `analogInput1Current`, `analogInput2Current`, `batteryVoltage`
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to never if not provided; if not provided then pagination will not cease, and a valid pagination cursor will always be returned. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  bool|null  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     * @param  bool|null  $includeTags  Optional boolean indicating whether to return tags on supported entities
     * @param  bool|null  $includeAttributes  Optional boolean indicating whether to return attributes on supported entities
     */
    public function __construct(
        protected array $ids,
        protected string $type,
        protected string $startTime,
        protected ?string $endTime = null,
        protected ?bool $includeExternalIds = null,
        protected ?bool $includeTags = null,
        protected ?bool $includeAttributes = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'ids'                => $this->ids,
            'type'               => $this->type,
            'startTime'          => $this->startTime,
            'endTime'            => $this->endTime,
            'includeExternalIds' => $this->includeExternalIds,
            'includeTags'        => $this->includeTags,
            'includeAttributes'  => $this->includeAttributes,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/assets/inputs/stream';
    }
}

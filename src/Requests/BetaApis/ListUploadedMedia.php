<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listUploadedMedia.
 *
 * This endpoint returns a list of all uploaded media (video and still images) matching query
 * parameters, with a maximum query range of one day. Additional media can be retrieved with the
 * [Create a media retrieval request](https://developers.samsara.com/reference/postmediaretrieval)
 * endpoint, and they will be included in the list after they are uploaded. Urls provided by this
 * endpoint expire in 8 hours.
 *
 *  <b>Rate limit:</b> 100 requests/min (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Media Retrieval** under the Safety & Cameras category when creating or
 * editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class ListUploadedMedia extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. You can specify up to 20 vehicles. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  array|null  $inputs  An optional list of desired camera inputs for which to return captured media. If empty, media for all available inputs will be returned.
     * @param  array|null  $mediaTypes  An optional list of desired media types for which to return captured media. If empty, media for all available media types will be returned. Possible options include: image, videoHighRes.
     * @param  array|null  $triggerReasons  An optional list of desired trigger reasons for which to return captured media. If empty, media for all available trigger reasons will be returned. Possible options include: api, panicButton, periodicStill, safetyEvent, tripEndStill, tripStartStill, videoRetrieval. videoRetrieval represents media captured for a dashboard video retrieval request.
     * @param  string  $startTime  A start time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. End time cannot be more than 24 hours after startTime. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $availableAfterTime  An optional timestamp in RFC 3339 format that can act as a cursor to track which media has previously been retrieved; only media whose availableAtTime comes after this parameter will be returned. Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00
     */
    public function __construct(
        protected string $vehicleIds,
        protected ?array $inputs,
        protected ?array $mediaTypes,
        protected ?array $triggerReasons,
        protected string $startTime,
        protected string $endTime,
        protected ?string $availableAfterTime = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'vehicleIds'         => $this->vehicleIds,
            'inputs'             => $this->inputs,
            'mediaTypes'         => $this->mediaTypes,
            'triggerReasons'     => $this->triggerReasons,
            'startTime'          => $this->startTime,
            'endTime'            => $this->endTime,
            'availableAfterTime' => $this->availableAfterTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/cameras/media';
    }
}

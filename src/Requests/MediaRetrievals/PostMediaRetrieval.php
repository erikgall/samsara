<?php

namespace ErikGall\Samsara\Requests\MediaRetrievals;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * postMediaRetrieval.
 *
 * This endpoint creates an asynchronous request to upload certain media from a device. The closest
 * available media to the requested timestamp is returned. Images and high-res videos are supported;
 * other types of media (e.g. hyperlapse, low-res) are planned to be supported in the future.
 * Currently, only unblurred media is supported. If a device is offline, the requested media will be
 * uploaded once it comes back online. Quota limits are enforced for media retrievals made through the
 * API. The Create a media retrieval request response includes information about the media retrieval
 * quota remaining for the organization. The media retrieval quota for the organization is reset at the
 * beginning of each month.The quota is expressed using seconds of High Resolution video. 10 still
 * images are equivalent to a 1 second of High Resolution footage.
 *
 *  <b>Rate limit:</b> 100
 * requests/min (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Write Media Retrieval** under the Safety
 * & Cameras category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class PostMediaRetrieval extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct() {}

    public function resolveEndpoint(): string
    {
        return '/cameras/media/retrieval';
    }
}

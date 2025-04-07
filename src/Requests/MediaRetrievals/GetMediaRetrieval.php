<?php

namespace ErikGall\Samsara\Requests\MediaRetrievals;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getMediaRetrieval.
 *
 * This endpoint returns media information corresponding to a retrieval ID. Retrieval IDs are
 * associated to prior [media retrieval
 * requests](https://developers.samsara.com/reference/postmediaretrieval). Urls provided by this
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
class GetMediaRetrieval extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $retrievalId  Retrieval ID associated with this media capture request. Examples: 2308cec4-82e0-46f1-8b3c-a3592e5cc21e
     */
    public function __construct(protected string $retrievalId) {}

    public function defaultQuery(): array
    {
        return array_filter(['retrievalId' => $this->retrievalId]);
    }

    public function resolveEndpoint(): string
    {
        return '/cameras/media/retrieval';
    }
}

<?php

namespace ErikGall\Samsara\Requests\Trailers;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listTrailers.
 *
 * List all trailers.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Trailers** under the Trailers category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class ListTrailers extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function __construct(
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null,
        protected ?int $limit = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['tagIds' => $this->tagIds, 'parentTagIds' => $this->parentTagIds, 'limit' => $this->limit]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/trailers';
    }
}

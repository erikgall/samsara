<?php

namespace ErikGall\Samsara\Requests\Addresses;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * listAddresses.
 *
 * Returns a list of all addresses in an organization.
 *
 *  **Submit Feedback**: Likes, dislikes, and API
 * feature requests should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69"
 * target="_blank">API feedback form</a>. If you encountered an issue or noticed inaccuracies in the
 * API documentation, please <a href="https://www.samsara.com/help" target="_blank">submit a case</a>
 * to our support team.
 *
 * To use this endpoint, select **Read Addresses** under the Addresses category
 * when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class ListAddresses extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @param  array|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     * @param  array|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $createdAfterTime  A filter on data to have a created at time after or equal to this specified time in RFC 3339 format. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     */
    public function __construct(
        protected ?int $limit = null,
        protected ?array $parentTagIds = null,
        protected ?array $tagIds = null,
        protected ?string $createdAfterTime = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit'            => $this->limit,
            'parentTagIds'     => $this->parentTagIds,
            'tagIds'           => $this->tagIds,
            'createdAfterTime' => $this->createdAfterTime,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/addresses';
    }
}

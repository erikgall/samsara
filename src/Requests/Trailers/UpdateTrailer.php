<?php

namespace ErikGall\Samsara\Requests\Trailers;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * updateTrailer.
 *
 * Update a trailer.  **Note** this implementation of patch uses [the JSON merge
 * patch](https://tools.ietf.org/html/rfc7396) proposed standard.
 *  This means that any fields included
 * in the patch request will _overwrite_ fields which exist on the target resource.
 *  For arrays, this
 * means any array included in the request will _replace_ the array that exists at the specified path,
 * it will not _add_ to the existing array
 *
 *  <b>Rate limit:</b> 100 requests/min (learn more about rate
 * limits <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use
 * this endpoint, select **Write Trailers** under the Trailers category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class UpdateTrailer extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  string  $id  ID of the trailer. Can be either unique Samsara ID or an [external ID](https://developers.samsara.com/docs/external-ids) for the trailer.
     */
    public function __construct(protected string $id, protected array $payload = []) {}

    public function resolveEndpoint(): string
    {
        return "/fleet/trailers/{$this->id}";
    }

    /**
     * Default body.
     *
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}

<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * patchJob.
 *
 * Patches an existing job and returns it.
 *
 * To use this endpoint, select **Write Jobs** under the
 * Equipment category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class PatchJob extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  string  $id  A jobId or uuid in STRING format. JobId must be prefixed with `jobId:`(Examples: `"8d218e6c-7a16-4f9f-90f7-cc1d93b9e596"`, `"jobId:98765"`).
     */
    public function __construct(protected string $id, protected array $payload = []) {}

    public function defaultQuery(): array
    {
        return array_filter(['id' => $this->id]);
    }

    public function resolveEndpoint(): string
    {
        return '/beta/industrial/jobs';
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

<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * postFormSubmissionsPdfExports.
 *
 * Creates a PDF export for a form submission.
 *
 * **Beta:** This endpoint is in beta and is likely to
 * change before being broadly available. Reach out to your Samsara Representative to have Forms APIs
 * enabled for your organization.
 *
 *  <b>Rate limit:</b> 100 requests/min (learn more about rate limits
 * <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Write Form Submissions** under the Closed Beta category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class PostFormSubmissionsPdfExports extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $id  ID of the form submission to create a PDF export from.
     */
    public function __construct(protected string $id, protected array $payload = []) {}

    public function defaultQuery(): array
    {
        return array_filter(['id' => $this->id]);
    }

    public function resolveEndpoint(): string
    {
        return '/form-submissions/pdf-exports';
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

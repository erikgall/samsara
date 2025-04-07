<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getFormSubmissionsPdfExports.
 *
 * Returns a PDF export for a form submission.
 *
 * **Beta:** This endpoint is in beta and is likely to
 * change before being broadly available. Reach out to your Samsara Representative to have Forms APIs
 * enabled for your organization.
 *
 *  <b>Rate limit:</b> 100 requests/min (learn more about rate limits
 * <a href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Form Submissions** under the Closed Beta category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetFormSubmissionsPdfExports extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $pdfId  ID of the form submission PDF export.
     */
    public function __construct(
        protected string $pdfId,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['pdfId' => $this->pdfId]);
    }

    public function resolveEndpoint(): string
    {
        return '/form-submissions/pdf-exports';
    }
}

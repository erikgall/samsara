<?php

namespace ErikGall\Samsara\Requests\Documents;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDocumentPdf.
 *
 * Returns generation job status and download URL for a PDF.
 *
 *  **Submit Feedback**: Likes, dislikes,
 * and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Documents** under the Driver Workflow category when creating or editing
 * an API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class GetDocumentPdf extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $id  ID of the pdf.
     */
    public function __construct(protected string $id) {}

    public function resolveEndpoint(): string
    {
        return "/fleet/documents/pdfs/{$this->id}";
    }
}

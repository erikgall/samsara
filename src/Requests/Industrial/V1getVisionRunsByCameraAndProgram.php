<?php

namespace ErikGall\Samsara\Requests\Industrial;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getVisionRunsByCameraAndProgram.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Fetch runs by camera and program.
 *
 *  **Submit Feedback**: Likes, dislikes,
 * and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Industrial** under the Industrial category when creating or editing an
 * API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getVisionRunsByCameraAndProgram extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $cameraId  The camera_id should be valid for the given accessToken.
     * @param  int  $programId  The configured program's ID on the camera.
     * @param  int  $startedAtMs  Started_at_ms is a required param. Indicates the start time of the run to be fetched.
     * @param  string|null  $include  Include is a filter parameter. Accepts 'pass', 'reject' or 'no_read'.
     */
    public function __construct(
        protected int $cameraId,
        protected int $programId,
        protected int $startedAtMs,
        protected ?string $include = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['include' => $this->include]);
    }

    public function resolveEndpoint(): string
    {
        return "/v1/industrial/vision/runs/{$this->cameraId}/{$this->programId}/{$this->startedAtMs}";
    }
}

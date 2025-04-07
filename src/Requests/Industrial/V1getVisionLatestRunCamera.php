<?php

namespace ErikGall\Samsara\Requests\Industrial;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getVisionLatestRunCamera.
 *
 * Fetch the latest run for a camera or program by default. If startedAtMs is supplied, fetch the
 * specific run that corresponds to that start time.
 *
 *  **Submit Feedback**: Likes, dislikes, and API
 * feature requests should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69"
 * target="_blank">API feedback form</a>. If you encountered an issue or noticed inaccuracies in the
 * API documentation, please <a href="https://www.samsara.com/help" target="_blank">submit a case</a>
 * to our support team.
 *
 * To use this endpoint, select **Read Industrial** under the Industrial category
 * when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getVisionLatestRunCamera extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $cameraId  The camera_id should be valid for the given accessToken.
     * @param  int|null  $programId  The configured program's ID on the camera.
     * @param  int|null  $startedAtMs  EndMs is an optional param. It will default to the current time.
     * @param  string|null  $include  Include is a filter parameter. Accepts 'pass', 'reject' or 'no_read'.
     * @param  int|null  $limit  Limit is an integer value from 1 to 1,000.
     */
    public function __construct(
        protected int $cameraId,
        protected ?int $programId = null,
        protected ?int $startedAtMs = null,
        protected ?string $include = null,
        protected ?int $limit = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'program_id'  => $this->programId,
            'startedAtMs' => $this->startedAtMs,
            'include'     => $this->include,
            'limit'       => $this->limit,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return "/v1/industrial/vision/run/camera/{$this->cameraId}";
    }
}

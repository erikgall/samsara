<?php

namespace ErikGall\Samsara\Requests\Industrial;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * V1getVisionProgramsByCamera.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Fetch configured programs on the camera.
 *
 *  **Submit Feedback**: Likes,
 * dislikes, and API feature requests should be filed as feedback in our <a
 * href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API feedback form</a>. If you encountered
 * an issue or noticed inaccuracies in the API documentation, please <a
 * href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 *
 * To use
 * this endpoint, select **Read Industrial** under the Industrial category when creating or editing an
 * API token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class V1getVisionProgramsByCamera extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $cameraId  The camera_id should be valid for the given accessToken.
     */
    public function __construct(protected int $cameraId) {}

    public function resolveEndpoint(): string
    {
        return "/v1/industrial/vision/cameras/{$this->cameraId}/programs";
    }
}

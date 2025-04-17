<?php

namespace ErikGall\Samsara\Requests\HoursOfService;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * setCurrentDutyStatus.
 *
 * <n class="warning">
 * <nh>
 * <i class="fa fa-exclamation-circle"></i>
 * This endpoint is still on our
 * legacy API.
 * </nh>
 * </n>
 *
 * Set an individual driver’s current duty status to 'On Duty' or 'Off
 * Duty'.
 *
 *  To ensure compliance with the ELD Mandate, only  authenticated drivers can make direct duty
 * status changes on their own logbook. Any system external to the Samsara Driver App using this
 * endpoint to trigger duty status changes must ensure that such changes are only triggered directly by
 * the driver in question and that the driver has been properly authenticated. This endpoint should not
 * be used to algorithmically trigger duty status changes nor should it be used by personnel besides
 * the driver to trigger duty status changes on the driver’s behalf. Carriers and their drivers are
 * ultimately responsible for maintaining accurate logs and should confirm that their use of the
 * endpoint is compliant with the ELD Mandate.
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature
 * requests should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69"
 * target="_blank">API feedback form</a>. If you encountered an issue or noticed inaccuracies in the
 * API documentation, please <a href="https://www.samsara.com/help" target="_blank">submit a case</a>
 * to our support team.
 *
 * To use this endpoint, select **Write ELD Hours of Service (US)** under the
 * Compliance category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 */
class SetCurrentDutyStatus extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  int  $driverId  ID of the driver for whom the duty status is being set.
     */
    public function __construct(protected int $driverId, protected array $payload = []) {}

    public function resolveEndpoint(): string
    {
        return "/v1/fleet/drivers/{$this->driverId}/hos/duty_status";
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

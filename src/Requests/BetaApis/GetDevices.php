<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getDevices.
 *
 * Returns all installed cameras (CM3x), vehicle gateways (VGs), and asset gateways (AGs) and their
 * health information within an organization.
 *
 * **Beta:** This endpoint is in beta and is likely to
 * change before being broadly available. Reach out to your Samsara Representative to have Devices API
 * enabled for your organization.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Devices** under the Devices category when creating or editing an API token.
 * <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetDevices extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $models  Optional string of comma separated device models. Valid values: `CM31`, `CM32`, `CM33`, `CM34`, `VG34`, `VG34M`, `VG34EU`, `VG34FN`, `VG54NA`, `VG54EU`, `VG55NA`, `VG55EU`, `AG24`, `AG24EU`, `AG26`, `AG26EU`, `AG45`, `AG45EU`, `AG46`, `AG46EU`, `AG46P`, `AG46PEU`, `AG51`, `AG51EU`, `AG52`, `AG52EU`, `AG53`, `AG53EU`
     * @param  array|null  $healthStatuses  Optional string of comma separated device health statuses. Valid values: `healthy`, `needsAttention`, `needsReplacement`, `dataPending`.
     * @param  bool|null  $includeHealth  Optional boolean to control whether device health information is returned in the response. Defaults to false.
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 100 objects.
     */
    public function __construct(
        protected ?array $models = null,
        protected ?array $healthStatuses = null,
        protected ?bool $includeHealth = null,
        protected ?int $limit = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'models'         => $this->models,
            'healthStatuses' => $this->healthStatuses,
            'includeHealth'  => $this->includeHealth,
            'limit'          => $this->limit,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/devices';
    }
}

<?php

namespace ErikGall\Samsara\Requests\Alerts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getConfigurations.
 *
 * Get specified Alert Configurations.
 *
 * The following trigger types are API enabled and will show up in
 * the results:
 * Vehicle Speed
 * Ambient Temperature
 * Fuel Level (Percentage)
 * Vehicle DEF Level
 * (Percentage)
 * Vehicle Battery
 * Gateway Unplugged
 * Dashcam Disconnected
 * Camera Connector
 * Disconnected
 * Asset starts moving
 * Inside Geofence
 * Outside Geofence
 * Unassigned Driving
 * Driver HOS
 * Violation
 * Vehicle Engine Idle
 * Asset Engine On
 * Asset Engine Off
 * Harsh Event
 * Scheduled
 * Maintenance
 * Scheduled Maintenance by Odometer
 * Scheduled Maintenance by Engine Hours
 * Out of Route
 * GPS
 * Signal Loss
 * Cell Signal Loss
 * Fault Code
 * Tire Faults
 * Gateway Disconnected
 * Panic Button
 * Tampering
 * Detected
 * If vehicle is severely speeding (as defined by your organization)
 * DVIR Submitted for
 * Asset
 * Driver Document Submitted
 * Driver App Sign In
 * Driver App Sign Out
 * Geofence Entry
 * Geofence
 * Exit
 * Route Stop ETA Alert
 * Driver Recorded
 * Sudden Fuel Level Rise
 * Sudden Fuel Level Drop
 * Scheduled
 * Date And Time
 * Training Assignment Due Soon
 * Training Assignment Past Due
 *
 *  <b>Rate limit:</b> 5
 * requests/sec (learn more about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read Alerts** under the Alerts category
 * when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetConfigurations extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $ids  Filter by the IDs. Returns all if no ids are provided.
     * @param  string|null  $status  The status of the alert configuration.  Valid values: `all`, `enabled`, `disabled`
     * @param  bool|null  $includeExternalIds  Optional boolean indicating whether to return external IDs on supported entities
     */
    public function __construct(
        protected ?array $ids = null,
        protected ?string $status = null,
        protected ?bool $includeExternalIds = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['ids' => $this->ids, 'status' => $this->status, 'includeExternalIds' => $this->includeExternalIds]);
    }

    public function resolveEndpoint(): string
    {
        return '/alerts/configurations';
    }
}

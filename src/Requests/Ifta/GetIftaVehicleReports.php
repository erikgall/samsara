<?php

namespace ErikGall\Samsara\Requests\Ifta;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getIftaVehicleReports.
 *
 * Get all vehicle IFTA reports for the requested time duration. Data is returned in your
 * organization's defined timezone.
 *
 * **Note:** The most recent 72 hours of data may still be processing
 * and is subject to change and latency, so it is not recommended to request data for the most recent
 * 72 hours.
 *
 *  <b>Rate limit:</b> 25 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read IFTA (US)** under the Compliance category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetIftaVehicleReports extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  int  $year  The year of the requested IFTA report summary. Must be provided with a month or quarter param. Example: `year=2021`
     * @param  string|null  $month  The month of the requested IFTA report summary. Can not be provided with the quarter param. Example: `month=January`  Valid values: `January`, `February`, `March`, `April`, `May`, `June`, `July`, `August`, `September`, `October`, `November`, `December`
     * @param  string|null  $quarter  The quarter of the requested IFTA report summary. Can not be provided with the month param. Q1: January, February, March. Q2: April, May, June. Q3: July, August, September. Q4: October, November, December. Example: `quarter=Q1`  Valid values: `Q1`, `Q2`, `Q3`, `Q4`
     * @param  string|null  $jurisdictions  A filter on the data based on this comma-separated list of jurisdictions. Example: `jurisdictions=GA`
     * @param  string|null  $fuelType  A filter on the data based on this comma-separated list of IFTA fuel types. Example: `fuelType=Diesel`  Valid values: `Unspecified`, `A55`, `Biodiesel`, `CompressedNaturalGas`, `Diesel`, `E85`, `Electricity`, `Ethanol`, `Gasohol`, `Gasoline`, `Hydrogen`, `LiquifiedNaturalGas`, `M85`, `Methanol`, `Propane`, `Other`
     * @param  string|null  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string|null  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string|null  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     */
    public function __construct(
        protected int $year,
        protected ?string $month = null,
        protected ?string $quarter = null,
        protected ?string $jurisdictions = null,
        protected ?string $fuelType = null,
        protected ?string $vehicleIds = null,
        protected ?string $tagIds = null,
        protected ?string $parentTagIds = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'year'          => $this->year,
            'month'         => $this->month,
            'quarter'       => $this->quarter,
            'jurisdictions' => $this->jurisdictions,
            'fuelType'      => $this->fuelType,
            'vehicleIds'    => $this->vehicleIds,
            'tagIds'        => $this->tagIds,
            'parentTagIds'  => $this->parentTagIds,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/fleet/reports/ifta/vehicle';
    }
}

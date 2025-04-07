<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Ifta\GetIftaDetailJob;
use ErikGall\Samsara\Requests\Ifta\CreateIftaDetailJob;
use ErikGall\Samsara\Requests\Ifta\GetIftaVehicleReports;
use ErikGall\Samsara\Requests\Ifta\GetIftaJurisdictionReports;

class Ifta extends Resource
{
    public function createIftaDetailJob(): Response
    {
        return $this->connector->send(new CreateIftaDetailJob);
    }

    /**
     * @param  string  $id  ID of the requested job.
     */
    public function getIftaDetailJob(string $id): Response
    {
        return $this->connector->send(new GetIftaDetailJob($id));
    }

    /**
     * @param  int  $year  The year of the requested IFTA report summary. Must be provided with a month or quarter param. Example: `year=2021`
     * @param  string  $month  The month of the requested IFTA report summary. Can not be provided with the quarter param. Example: `month=January`  Valid values: `January`, `February`, `March`, `April`, `May`, `June`, `July`, `August`, `September`, `October`, `November`, `December`
     * @param  string  $quarter  The quarter of the requested IFTA report summary. Can not be provided with the month param. Q1: January, February, March. Q2: April, May, June. Q3: July, August, September. Q4: October, November, December. Example: `quarter=Q1`  Valid values: `Q1`, `Q2`, `Q3`, `Q4`
     * @param  string  $jurisdictions  A filter on the data based on this comma-separated list of jurisdictions. Example: `jurisdictions=GA`
     * @param  string  $fuelType  A filter on the data based on this comma-separated list of IFTA fuel types. Example: `fuelType=Diesel`  Valid values: `Unspecified`, `A55`, `Biodiesel`, `CompressedNaturalGas`, `Diesel`, `E85`, `Electricity`, `Ethanol`, `Gasohol`, `Gasoline`, `Hydrogen`, `LiquifiedNaturalGas`, `M85`, `Methanol`, `Propane`, `Other`
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     */
    public function getIftaJurisdictionReports(
        int $year,
        ?string $month,
        ?string $quarter,
        ?string $jurisdictions,
        ?string $fuelType,
        ?string $vehicleIds,
        ?string $tagIds,
        ?string $parentTagIds,
    ): Response {
        return $this->connector->send(new GetIftaJurisdictionReports($year, $month, $quarter, $jurisdictions, $fuelType, $vehicleIds, $tagIds, $parentTagIds));
    }

    /**
     * @param  int  $year  The year of the requested IFTA report summary. Must be provided with a month or quarter param. Example: `year=2021`
     * @param  string  $month  The month of the requested IFTA report summary. Can not be provided with the quarter param. Example: `month=January`  Valid values: `January`, `February`, `March`, `April`, `May`, `June`, `July`, `August`, `September`, `October`, `November`, `December`
     * @param  string  $quarter  The quarter of the requested IFTA report summary. Can not be provided with the month param. Q1: January, February, March. Q2: April, May, June. Q3: July, August, September. Q4: October, November, December. Example: `quarter=Q1`  Valid values: `Q1`, `Q2`, `Q3`, `Q4`
     * @param  string  $jurisdictions  A filter on the data based on this comma-separated list of jurisdictions. Example: `jurisdictions=GA`
     * @param  string  $fuelType  A filter on the data based on this comma-separated list of IFTA fuel types. Example: `fuelType=Diesel`  Valid values: `Unspecified`, `A55`, `Biodiesel`, `CompressedNaturalGas`, `Diesel`, `E85`, `Electricity`, `Ethanol`, `Gasohol`, `Gasoline`, `Hydrogen`, `LiquifiedNaturalGas`, `M85`, `Methanol`, `Propane`, `Other`
     * @param  string  $vehicleIds  A filter on the data based on this comma-separated list of vehicle IDs and externalIds. Example: `vehicleIds=1234,5678,samsara.vin:1HGBH41JXMN109186`
     * @param  string  $tagIds  A filter on the data based on this comma-separated list of tag IDs. Example: `tagIds=1234,5678`
     * @param  string  $parentTagIds  A filter on the data based on this comma-separated list of parent tag IDs, for use by orgs with tag hierarchies. Specifying a parent tag will implicitly include all descendent tags of the parent tag. Example: `parentTagIds=345,678`
     */
    public function getIftaVehicleReports(
        int $year,
        ?string $month,
        ?string $quarter,
        ?string $jurisdictions,
        ?string $fuelType,
        ?string $vehicleIds,
        ?string $tagIds,
        ?string $parentTagIds,
    ): Response {
        return $this->connector->send(new GetIftaVehicleReports($year, $month, $quarter, $jurisdictions, $fuelType, $vehicleIds, $tagIds, $parentTagIds));
    }
}

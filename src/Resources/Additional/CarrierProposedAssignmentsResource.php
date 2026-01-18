<?php

namespace Samsara\Resources\Additional;

use Samsara\Resources\Resource;

/**
 * CarrierProposedAssignments resource for the Samsara API.
 *
 * Provides access to carrier proposed driver-vehicle assignments endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CarrierProposedAssignmentsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/carrier-proposed-assignments';
}

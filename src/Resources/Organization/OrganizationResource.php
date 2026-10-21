<?php

namespace Samsara\Resources\Organization;

use Samsara\Data\Entity;
use Samsara\Resources\Resource;
use Samsara\Data\Organization\Organization;

/**
 * Current organization resource for the Samsara API.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class OrganizationResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/me';

    /**
     * Get the organization associated with the current token.
     */
    public function current(): Organization
    {
        $response = $this->executeSdkGet($this->endpoint);

        return Organization::make($this->decodeJsonData($response, $this->endpoint, false));
    }
}

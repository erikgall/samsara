<?php

namespace ErikGall\Samsara\Resources\Integrations;

use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\Vehicle\Gateway;

/**
 * Gateways resource for the Samsara API.
 *
 * Provides access to gateway device management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class GatewaysResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/gateways';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Gateway>
     */
    protected string $entity = Gateway::class;
}

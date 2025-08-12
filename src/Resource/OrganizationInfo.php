<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\OrganizationInfo\GetOrganizationInfo;

class OrganizationInfo extends Resource
{
    /**
     * Get organization info.
     *
     * @return Response
     */
    public function get(): Response
    {
        return $this->connector->send(new GetOrganizationInfo);
    }
}

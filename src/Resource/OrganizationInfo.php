<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\OrganizationInfo\GetOrganizationInfo;

class OrganizationInfo extends Resource
{
    public function get(): Response
    {
        return $this->connector->send(new GetOrganizationInfo);
    }
}

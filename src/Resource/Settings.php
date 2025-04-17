<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Settings\GetSafetySettings;
use ErikGall\Samsara\Requests\Settings\GetDriverAppSettings;
use ErikGall\Samsara\Requests\Settings\GetComplianceSettings;
use ErikGall\Samsara\Requests\Settings\PatchDriverAppSettings;
use ErikGall\Samsara\Requests\Settings\PatchComplianceSettings;

class Settings extends Resource
{
    public function getComplianceSettings(): Response
    {
        return $this->connector->send(new GetComplianceSettings);
    }

    public function getDriverAppSettings(): Response
    {
        return $this->connector->send(new GetDriverAppSettings);
    }

    public function getSafetySettings(): Response
    {
        return $this->connector->send(new GetSafetySettings);
    }

    public function patchComplianceSettings(array $payload = []): Response
    {
        return $this->connector->send(new PatchComplianceSettings($payload));
    }

    public function patchDriverAppSettings(array $payload = []): Response
    {
        return $this->connector->send(new PatchDriverAppSettings($payload));
    }
}

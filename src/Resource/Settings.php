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
    /**
     * Get compliance settings.
     *
     * @return Response
     */
    public function getComplianceSettings(): Response
    {
        return $this->connector->send(new GetComplianceSettings);
    }

    /**
     * Get driver app settings.
     *
     * @return Response
     */
    public function getDriverAppSettings(): Response
    {
        return $this->connector->send(new GetDriverAppSettings);
    }

    /**
     * Get safety settings.
     *
     * @return Response
     */
    public function getSafetySettings(): Response
    {
        return $this->connector->send(new GetSafetySettings);
    }

    /**
     * Update compliance settings.
     *
     * @param  array  $payload
     * @return Response
     */
    public function updateComplianceSettings(array $payload = []): Response
    {
        return $this->connector->send(new PatchComplianceSettings($payload));
    }

    /**
     * Update driver app settings.
     *
     * @param  array  $payload
     * @return Response
     */
    public function updateDriverAppSettings(array $payload = []): Response
    {
        return $this->connector->send(new PatchDriverAppSettings($payload));
    }
}

<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Resources\Resource;

/**
 * Settings resource for the Samsara API.
 *
 * Provides access to organization settings endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SettingsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/settings';

    /**
     * Get compliance settings.
     *
     * @return array<string, mixed>
     */
    public function compliance(): array
    {
        $response = $this->client()->get('/settings/compliance');

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Get driver app settings.
     *
     * @return array<string, mixed>
     */
    public function driverApp(): array
    {
        $response = $this->client()->get('/settings/driver-app');

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Get safety settings.
     *
     * @return array<string, mixed>
     */
    public function safety(): array
    {
        $response = $this->client()->get('/settings/safety');

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Update compliance settings.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateCompliance(array $data): array
    {
        $response = $this->client()->patch('/settings/compliance', $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Update driver app settings.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateDriverApp(array $data): array
    {
        $response = $this->client()->patch('/settings/driver-app', $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }
}

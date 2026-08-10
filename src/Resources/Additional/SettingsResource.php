<?php

namespace Samsara\Resources\Additional;

use Samsara\Resources\Resource;

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
    protected string $endpoint = '/fleet/settings';

    /**
     * Get compliance settings.
     *
     * @return array<string, mixed>
     */
    public function compliance(): array
    {
        $endpoint = '/fleet/settings/compliance';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint),
            $endpoint
        );

        return $response->json('data', $response->json());
    }

    /**
     * Get driver app settings.
     *
     * @return array<string, mixed>
     */
    public function driverApp(): array
    {
        $endpoint = '/fleet/settings/driver-app';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint),
            $endpoint
        );

        return $response->json('data', $response->json());
    }

    /**
     * Get safety settings.
     *
     * @return array<int, array<string, mixed>>
     */
    public function safety(): array
    {
        $endpoint = '/fleet/settings/safety';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint),
            $endpoint
        );

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
        $endpoint = '/fleet/settings/compliance';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($endpoint, $data),
            $endpoint
        );

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
        $endpoint = '/fleet/settings/driver-app';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($endpoint, $data),
            $endpoint
        );

        return $response->json('data', $response->json());
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return [];
    }
}

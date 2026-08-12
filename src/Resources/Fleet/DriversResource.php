<?php

namespace Samsara\Resources\Fleet;

use Samsara\Query\Builder;
use Samsara\Data\Driver\Driver;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
use Samsara\Data\Driver\DriverQrCode;
use Samsara\Data\Driver\DriverAuthToken;
use Samsara\Data\Driver\DriverRemoteSignOutResult;

/**
 * Drivers resource for the Samsara API.
 *
 * Provides access to driver-related endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriversResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/drivers';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Driver>
     */
    protected string $entity = Driver::class;

    /**
     * Activate a driver.
     */
    public function activate(string $id): Driver
    {
        /** @var Driver */
        return $this->update($id, ['driverActivationStatus' => 'active']);
    }

    /**
     * Get a query builder filtered for active drivers.
     */
    public function active(): Builder
    {
        return $this->query()->where('driverActivationStatus', 'active');
    }

    /**
     * Create an authentication token for a driver.
     *
     * Generates a session token that can be used for driver authentication.
     *
     * @param  array<string, mixed>  $data
     */
    public function createAuthToken(array $data): DriverAuthToken
    {
        $endpoint = '/fleet/drivers/auth-token';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return DriverAuthToken::make($response->json('data', []));
    }

    /**
     * Create a QR code for driver app login.
     *
     * @param  array<string, mixed>  $data
     */
    public function createQrCode(array $data): DriverQrCode
    {
        $endpoint = '/drivers/qr-codes';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return DriverQrCode::make($response->json('data', []));
    }

    /**
     * Deactivate a driver.
     */
    public function deactivate(string $id): Driver
    {
        /** @var Driver */
        return $this->update($id, ['driverActivationStatus' => 'deactivated']);
    }

    /**
     * Get a query builder filtered for deactivated drivers.
     */
    public function deactivated(): Builder
    {
        return $this->query()->where('driverActivationStatus', 'deactivated');
    }

    /**
     * Delete a QR code.
     */
    public function deleteQrCode(int $driverId): bool
    {
        $endpoint = '/drivers/qr-codes';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->delete($endpoint, ['driverId' => $driverId]),
            $endpoint
        );

        return $response->successful();
    }

    /**
     * Find a driver by external ID.
     *
     * @param  string  $key  The external ID key
     * @param  string  $value  The external ID value
     */
    public function findByExternalId(string $key, string $value): ?Driver
    {
        /** @var Driver */
        return $this->find(rawurlencode($key).':'.rawurlencode($value));
    }

    /**
     * Get all QR codes for driver app login.
     *
     * @param  array<int, int|string>|string  $driverIds
     * @return EntityCollection<int, DriverQrCode>
     */
    public function getQrCodes(array|string $driverIds): EntityCollection
    {
        $endpoint = '/drivers/qr-codes';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, [
                'driverIds' => is_array($driverIds) ? implode(',', $driverIds) : $driverIds,
            ]),
            $endpoint
        );

        /** @var array<int, array<string, mixed>> $data */
        $data = $response->json('data', []);

        return new EntityCollection(array_map(
            fn (array $item): DriverQrCode => DriverQrCode::make($item),
            $data
        ));
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }

    /**
     * Remotely sign out a driver from the driver app.
     */
    public function remoteSignOut(string $id): DriverRemoteSignOutResult
    {
        $endpoint = '/fleet/drivers/remote-sign-out';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, ['driverId' => $id]),
            $endpoint
        );

        return DriverRemoteSignOutResult::make($response->json());
    }

    /**
     * Get the inherited generic operations supported by this resource.
     *
     * @return array<int, string>
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'find', 'create', 'update'];
    }
}

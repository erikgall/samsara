<?php

namespace Samsara\Resources\Fleet;

use Samsara\Query\Builder;
use Samsara\Data\Driver\Driver;
use Samsara\Resources\Resource;
use Illuminate\Support\Collection;

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
     */
    public function createAuthToken(string $id): string
    {
        $response = $this->client()->post("{$this->endpoint}/{$id}/auth-tokens");

        $this->handleError($response);

        return $response->json('token', '');
    }

    /**
     * Create a QR code for driver app login.
     *
     * @param  array<string, mixed>  $data
     */
    public function createQrCode(array $data): object
    {
        $response = $this->client()->post("{$this->endpoint}/qr-codes", $data);

        $this->handleError($response);

        return (object) $response->json('data', $response->json());
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
    public function deleteQrCode(string $id): bool
    {
        $response = $this->client()->delete("{$this->endpoint}/qr-codes/{$id}");

        $this->handleError($response);

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
        $result = $this->query()
            ->where("externalIds[{$key}]", $value)
            ->first();

        if ($result === null) {
            return null;
        }

        /** @var Driver */
        return $result;
    }

    /**
     * Get all QR codes for driver app login.
     *
     * @return Collection<int, object>
     */
    public function getQrCodes(): Collection
    {
        $response = $this->client()->get("{$this->endpoint}/qr-codes");

        $this->handleError($response);

        $data = $response->json('data', []);

        return new Collection(array_map(fn (array $item) => (object) $item, $data));
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
    public function remoteSignOut(string $id): void
    {
        $response = $this->client()->post("{$this->endpoint}/{$id}/remote-sign-out");

        $this->handleError($response);
    }
}

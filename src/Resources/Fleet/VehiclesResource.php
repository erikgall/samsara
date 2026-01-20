<?php

namespace Samsara\Resources\Fleet;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Vehicle\Vehicle;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Vehicles resource for the Samsara API.
 *
 * Provides access to vehicle-related endpoints.
 *
 * Note: Vehicles are automatically created when a Samsara Vehicle Gateway is installed.
 * To manually create vehicles, use the Assets API (POST /assets with type: "vehicle").
 * Vehicles cannot be deleted; they can only be marked as retired via the update method.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehiclesResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/vehicles';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Vehicle>
     */
    protected string $entity = Vehicle::class;

    /**
     * Create a new vehicle.
     *
     * Note: This operation is not supported via the /fleet/vehicles endpoint.
     * Vehicles are automatically created when a Samsara Vehicle Gateway is installed.
     * To manually create vehicles, use the Assets API: POST /assets with type "vehicle".
     *
     * @param  array<string, mixed>  $data
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data): object
    {
        throw new UnsupportedOperationException(
            'Vehicles cannot be created via /fleet/vehicles. '
            .'Vehicles are automatically created when a Samsara Vehicle Gateway is installed. '
            .'To manually create vehicles, use the Assets API: $samsara->assets()->create([\'type\' => \'vehicle\', ...]).'
        );
    }

    /**
     * Delete a vehicle.
     *
     * Note: This operation is not supported by the Samsara API.
     * Vehicles cannot be deleted for compliance reasons.
     * To retire a vehicle, update its name or notes field instead.
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id): bool
    {
        throw new UnsupportedOperationException(
            'Vehicles cannot be deleted via the Samsara API. '
            .'To retire a vehicle, update its name or notes field instead: '
            .'$samsara->vehicles()->update($id, [\'name\' => \'[RETIRED] Vehicle Name\']).'
        );
    }

    /**
     * Find a vehicle by external ID.
     *
     * @param  string  $key  The external ID key
     * @param  string  $value  The external ID value
     */
    public function findByExternalId(string $key, string $value): ?Vehicle
    {
        $result = $this->query()
            ->where("externalIds[{$key}]", $value)
            ->first();

        if ($result === null) {
            return null;
        }

        /** @var Vehicle */
        return $result;
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}

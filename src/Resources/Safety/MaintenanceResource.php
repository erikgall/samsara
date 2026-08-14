<?php

namespace Samsara\Resources\Safety;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Maintenance\Dvir;
use Samsara\Data\Maintenance\Defect;
use Samsara\Data\Maintenance\DefectType;

/**
 * Maintenance resource for the Samsara API.
 *
 * Provides access to DVIRs and defects endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class MaintenanceResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/dvirs/stream';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Dvir>
     */
    protected string $entity = Dvir::class;

    /**
     * Create a new DVIR.
     *
     * @param  array<string, mixed>  $data  The DVIR data
     */
    public function createDvir(array $data): Dvir
    {
        $endpoint = '/fleet/dvirs';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        /** @var Dvir */
        return $this->mapToEntity($this->decodeJsonData($response, $endpoint, false));
    }

    /**
     * Get a query builder for defects.
     */
    public function defects(): Builder
    {
        return $this->createBuilderWithEndpoint('/defects/stream')
            ->usingEntity(Defect::class);
    }

    /**
     * Get a query builder for defect types.
     */
    public function defectTypes(): Builder
    {
        return $this->createBuilderWithEndpoint('/defect-types')
            ->withoutDefaultPageSize()
            ->usingEntity(DefectType::class);
    }

    /**
     * Get a query builder for DVIRs.
     */
    public function dvirs(): Builder
    {
        return $this->query();
    }

    /**
     * Get one defect from its current item endpoint.
     *
     * @param  array<string, mixed>  $params
     */
    public function findDefect(string $id, array $params = []): Defect
    {
        $endpoint = '/defects/'.rawurlencode($id);
        $response = $this->executeSdkGet($endpoint, $params);

        return Defect::make($this->decodeJsonArray($response, $endpoint));
    }

    /**
     * Get one DVIR from its current item endpoint.
     *
     * @param  array<string, mixed>  $params
     */
    public function findDvir(string $id, array $params = []): Dvir
    {
        $endpoint = '/dvirs/'.rawurlencode($id);
        $response = $this->executeSdkGet($endpoint, $params);

        return Dvir::make($this->decodeJsonArray($response, $endpoint));
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return $this->createBuilderWithEndpoint($this->endpoint)
            ->repeatQueryParameters('safetyStatus');
    }

    /**
     * Resolve a DVIR at the fleet item endpoint.
     *
     * @param  array<string, mixed>  $data
     */
    public function resolveDvir(string $id, array $data): Dvir
    {
        $endpoint = '/fleet/dvirs/'.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($endpoint, $data),
            $endpoint,
        );

        return Dvir::make($this->decodeJsonData($response, $endpoint, false));
    }

    /**
     * Update a defect at the fleet item endpoint.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateDefect(string $id, array $data): Defect
    {
        $endpoint = '/fleet/defects/'.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($endpoint, $data),
            $endpoint,
        );

        return Defect::make($this->decodeJsonData($response, $endpoint, false));
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return [];
    }
}

<?php

namespace Samsara\Resources\Safety;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Maintenance\Dvir;
use Samsara\Data\Maintenance\Defect;

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

        return Dvir::make($response->json('data', []));
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
     * Get a query builder for DVIRs.
     */
    public function dvirs(): Builder
    {
        return $this->query();
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return (new Builder($this))->repeatQueryParameters('safetyStatus');
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return [];
    }
}

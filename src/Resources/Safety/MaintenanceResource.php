<?php

namespace ErikGall\Samsara\Resources\Safety;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\Maintenance\Dvir;

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
        $response = $this->client()->post('/dvirs', $data);

        return new Dvir($response->json('data'));
    }

    /**
     * Get a query builder for defects.
     */
    public function defects(): Builder
    {
        return $this->createBuilderWithEndpoint('/defects/stream');
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
        return new Builder($this);
    }

    /**
     * Create a builder with a custom endpoint.
     */
    protected function createBuilderWithEndpoint(string $endpoint): Builder
    {
        $originalEndpoint = $this->endpoint;
        $this->endpoint = $endpoint;
        $builder = new Builder($this);
        $this->endpoint = $originalEndpoint;

        return $builder;
    }
}

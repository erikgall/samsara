<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Resources\Concerns\CreatesEntities;

/**
 * Issues resource for the Samsara API.
 *
 * Provides access to vehicle/equipment issue tracking endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class IssuesResource extends Resource
{
    /** @use CreatesEntities<Entity> */
    use CreatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/issues';

    /**
     * Get a query builder for issues by ID.
     *
     * @return Builder<Entity>
     */
    public function query(): Builder
    {
        return $this->newQuery()->withoutDefaultPageSize();
    }

    /**
     * Get a query builder for issues stream.
     *
     * @return Builder<Entity>
     */
    public function stream(): Builder
    {
        return $this->newQuery('/issues/stream')
            ->withoutDefaultPageSize();
    }

    /**
     * Update an issue using the contract's collection-level PATCH operation.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateIssue(array $data): Entity
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($this->endpoint, $data),
            $this->endpoint,
        );

        return Entity::make($this->decodeJsonData($response, $this->endpoint, false));
    }
}

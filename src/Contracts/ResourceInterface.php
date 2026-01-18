<?php

namespace Samsara\Contracts;

use Samsara\Data\EntityCollection;

/**
 * Interface for API resource classes.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
interface ResourceInterface
{
    /**
     * Get all entities from this resource.
     *
     * @return EntityCollection<int, \Samsara\Data\Entity>
     */
    public function all(): EntityCollection;

    /**
     * Create a new entity.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): object;

    /**
     * Delete an entity.
     */
    public function delete(string $id): bool;

    /**
     * Find an entity by ID.
     */
    public function find(string $id): ?object;

    /**
     * Get the API endpoint for this resource.
     */
    public function getEndpoint(): string;

    /**
     * Update an entity.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): object;
}

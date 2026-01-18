<?php

namespace ErikGall\Samsara\Data;

use Illuminate\Support\Collection;

/**
 * Collection class for Entity objects.
 *
 * Extends Laravel's Collection with entity-specific helper methods.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @template TKey of array-key
 * @template TValue of Entity
 *
 * @extends Collection<TKey, TValue>
 */
class EntityCollection extends Collection
{
    /**
     * Find an entity by its ID.
     *
     * @return TValue|null
     */
    public function findById(string $id): ?Entity
    {
        return $this->first(function (Entity $entity) use ($id) {
            return $entity->getId() === $id;
        });
    }

    /**
     * Get all entity IDs.
     *
     * @return array<int, string>
     */
    public function ids(): array
    {
        /** @var array<int, string> */
        return $this->map(function (Entity $entity) {
            return $entity->getId();
        })->filter()->values()->all();
    }
}

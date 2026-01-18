<?php

namespace Samsara\Data;

use Illuminate\Support\Fluent;
use Samsara\Contracts\EntityInterface;

/**
 * Base entity class for API DTOs.
 *
 * Extends Laravel's Fluent class for convenient attribute access
 * and implements EntityInterface for consistent entity handling.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends Fluent<string, mixed>
 */
class Entity extends Fluent implements EntityInterface
{
    /**
     * Get the entity ID.
     */
    public function getId(): ?string
    {
        $id = $this->get('id');

        if ($id === null) {
            return null;
        }

        return (string) $id;
    }
}

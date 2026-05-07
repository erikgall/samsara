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
 *
 * @phpstan-consistent-constructor
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

    /**
     * Create a new entity instance from an attribute array.
     *
     * Defined locally so the SDK does not depend on Illuminate\Support\Fluent::make(),
     * which was only added in Laravel 12.9. Parameter type is `mixed` to remain
     * LSP-compatible with Fluent::make's untyped signature on Laravel ^12.9 and 13.
     *
     * @param  iterable<string, mixed>  $attributes
     */
    public static function make(mixed $attributes = []): static
    {
        $class = static::class;

        return new $class($attributes);
    }
}

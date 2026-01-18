<?php

namespace Samsara\Concerns;

/**
 * Trait for static factory method.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @phpstan-ignore trait.unused
 */
trait Makeable
{
    /**
     * Create a new instance.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @phpstan-ignore new.static
     */
    public static function make(array $attributes = []): static
    {
        return new static($attributes);
    }
}

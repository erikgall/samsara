<?php

namespace ErikGall\Samsara\Entities;

use Saloon\Traits\Responses\HasResponse;
use Illuminate\Contracts\Support\Arrayable;
use Saloon\Contracts\DataObjects\WithResponse;

/**
 * Base entity DTO.
 *
 * @author Erik Galloway <egalloway@boltsystem.com>
 */
class Entity implements Arrayable, WithResponse
{
    use HasResponse;

    /**
     * The entity's attributes.
     *
     * @var array
     */
    public array $attributes = [];

    /**
     * Make a new Driver instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = (array) $attributes;
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Allow dynamic access to the entity's attributes.
     *
     * @param  string  $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Allow dynamic setting of the entity's attributes.
     *
     * @param  string  $name
     * @param  mixed  $value
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
}

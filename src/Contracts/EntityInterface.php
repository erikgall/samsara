<?php

namespace ErikGall\Samsara\Contracts;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface for entity DTOs.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends Arrayable<string, mixed>
 */
interface EntityInterface extends Arrayable, Jsonable
{
    /**
     * Get the entity ID.
     */
    public function getId(): ?string;
}

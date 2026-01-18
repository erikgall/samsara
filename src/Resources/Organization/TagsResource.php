<?php

namespace Samsara\Resources\Organization;

use Samsara\Data\Tag\Tag;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * Tags resource for the Samsara API.
 *
 * Provides access to tag management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TagsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/tags';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Tag>
     */
    protected string $entity = Tag::class;

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}

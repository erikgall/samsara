<?php

namespace Samsara\Resources\Organization;

use Samsara\Data\Tag\Tag;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Tags resource for the Samsara API.
 *
 * Provides access to tag management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Tag>
 */
class TagsResource extends Resource
{
    /** @use CreatesEntities<Tag> */
    use CreatesEntities;

    /** @use DeletesEntities<Tag> */
    use DeletesEntities;

    /** @use FindsEntities<Tag> */
    use FindsEntities;

    /** @use UpdatesEntities<Tag> */
    use UpdatesEntities;

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
     *
     * @return Builder<Tag>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}

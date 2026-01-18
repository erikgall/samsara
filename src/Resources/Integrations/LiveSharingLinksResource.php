<?php

namespace Samsara\Resources\Integrations;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\LiveShare\LiveShare;

/**
 * LiveSharingLinks resource for the Samsara API.
 *
 * Provides access to live sharing link management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class LiveSharingLinksResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/live-shares';

    /**
     * The entity class for this resource.
     *
     * @var class-string<LiveShare>
     */
    protected string $entity = LiveShare::class;

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}

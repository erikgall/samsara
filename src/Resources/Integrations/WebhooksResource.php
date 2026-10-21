<?php

namespace Samsara\Resources\Integrations;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Webhook\Webhook;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Webhooks resource for the Samsara API.
 *
 * Provides access to webhook management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Webhook>
 */
class WebhooksResource extends Resource
{
    /** @use CreatesEntities<Webhook> */
    use CreatesEntities;

    /** @use DeletesEntities<Webhook> */
    use DeletesEntities;

    /** @use FindsEntities<Webhook> */
    use FindsEntities;

    /** @use UpdatesEntities<Webhook> */
    use UpdatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/webhooks';

    /**
     * The entity class for this resource.
     */
    protected string $entity = Webhook::class;

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Webhook>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}

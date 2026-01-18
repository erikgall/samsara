<?php

namespace Samsara\Resources\Integrations;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Webhook\Webhook;

/**
 * Webhooks resource for the Samsara API.
 *
 * Provides access to webhook management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WebhooksResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/webhooks';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Webhook>
     */
    protected string $entity = Webhook::class;

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}

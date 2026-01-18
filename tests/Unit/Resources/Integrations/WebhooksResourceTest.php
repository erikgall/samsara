<?php

namespace Samsara\Tests\Unit\Resources\Integrations;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Webhook\Webhook;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Integrations\WebhooksResource;

/**
 * Unit tests for the WebhooksResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WebhooksResourceTest extends TestCase
{
    protected HttpFactory $http;

    protected Samsara $samsara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = new HttpFactory;
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_create_a_webhook(): void
    {
        $this->http->fake([
            '*/webhooks' => $this->http->response([
                'data' => [
                    'id'   => 'webhook-123',
                    'name' => 'New Webhook',
                    'url'  => 'https://example.com/webhook',
                ],
            ], 201),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $webhook = $resource->create([
            'name' => 'New Webhook',
            'url'  => 'https://example.com/webhook',
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame('New Webhook', $webhook->name);
    }

    #[Test]
    public function it_can_create_query_builder(): void
    {
        $resource = new WebhooksResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_delete_a_webhook(): void
    {
        $this->http->fake([
            '*/webhooks/webhook-123' => $this->http->response([], 204),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $result = $resource->delete('webhook-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_a_webhook(): void
    {
        $this->http->fake([
            '*/webhooks/webhook-123' => $this->http->response([
                'data' => [
                    'id'   => 'webhook-123',
                    'name' => 'Test Webhook',
                ],
            ], 200),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $webhook = $resource->find('webhook-123');

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame('webhook-123', $webhook->id);
    }

    #[Test]
    public function it_can_get_all_webhooks(): void
    {
        $this->http->fake([
            '*/webhooks' => $this->http->response([
                'data' => [
                    ['id' => 'webhook-1', 'name' => 'Webhook 1'],
                    ['id' => 'webhook-2', 'name' => 'Webhook 2'],
                ],
            ], 200),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_update_a_webhook(): void
    {
        $this->http->fake([
            '*/webhooks/webhook-123' => $this->http->response([
                'data' => [
                    'id'   => 'webhook-123',
                    'name' => 'Updated Webhook',
                ],
            ], 200),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $webhook = $resource->update('webhook-123', ['name' => 'Updated Webhook']);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame('Updated Webhook', $webhook->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new WebhooksResource($this->samsara);

        $this->assertSame('/webhooks', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new WebhooksResource($this->samsara);

        $this->assertSame(Webhook::class, $resource->getEntityClass());
    }
}

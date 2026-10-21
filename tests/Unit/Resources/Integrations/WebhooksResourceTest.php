<?php

namespace Samsara\Tests\Unit\Resources\Integrations;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Webhook\Webhook;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_create_a_webhook(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'id'        => 'webhook-123',
                'name'      => 'New Webhook',
                'url'       => 'https://example.com/webhook',
                'secretKey' => 'secret-123',
                'version'   => '2021-06-09',
            ], 200),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $webhook = $resource->create([
            'name' => 'New Webhook',
            'url'  => 'https://example.com/webhook',
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame('New Webhook', $webhook->name);
        $this->assertSame('secret-123', $webhook->secretKey);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/webhooks'
            && $request->data() === [
                'name' => 'New Webhook',
                'url'  => 'https://example.com/webhook',
            ]
        );
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
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/webhooks/webhook-123'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_can_find_a_webhook(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'id'        => 'webhook-123',
                'name'      => 'Test Webhook',
                'url'       => 'https://example.com/webhook',
                'secretKey' => 'secret-123',
                'version'   => '2021-06-09',
            ], 200),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $webhook = $resource->find('webhook-123');

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame('webhook-123', $webhook->id);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/webhooks/webhook-123'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_can_update_a_webhook(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'id'        => 'webhook-123',
                'name'      => 'Updated Webhook',
                'url'       => 'https://example.com/webhook',
                'secretKey' => 'secret-123',
                'version'   => '2021-06-09',
            ], 200),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $webhook = $resource->update('webhook-123', ['name' => 'Updated Webhook']);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertSame('Updated Webhook', $webhook->name);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/webhooks/webhook-123'
            && $request->data() === ['name' => 'Updated Webhook']
        );
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

    #[Test]
    public function it_queries_webhooks(): void
    {
        $this->http->fake([
            '*/webhooks' => $this->http->response([
                'data' => [
                    [
                        'id'        => 'webhook-1',
                        'name'      => 'Webhook 1',
                        'url'       => 'https://example.com/webhook-1',
                        'secretKey' => 'secret-1',
                        'version'   => '2021-06-09',
                    ],
                    [
                        'id'        => 'webhook-2',
                        'name'      => 'Webhook 2',
                        'url'       => 'https://example.com/webhook-2',
                        'secretKey' => 'secret-2',
                        'version'   => '2021-06-09',
                    ],
                ],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ], 200),
        ]);

        $resource = new WebhooksResource($this->samsara);

        $result = $resource->query()->get();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Webhook::class, $result->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/webhooks'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_terminal_executes_filtered_webhook_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'        => 'webhook-1',
                    'name'      => 'Webhook 1',
                    'url'       => 'https://example.com/webhook-1',
                    'secretKey' => 'secret-1',
                    'version'   => '2021-06-09',
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new WebhooksResource($this->samsara))
            ->query()
            ->where('ids', 'webhook-1,webhook-2')
            ->after('webhook-cursor')
            ->paginate(50);

        $this->assertInstanceOf(Webhook::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/webhooks'
            && $request->data() == [
                'ids'   => 'webhook-1,webhook-2',
                'after' => 'webhook-cursor',
                'limit' => 50,
            ]
        );
    }
}

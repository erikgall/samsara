<?php

namespace Samsara\Tests\Unit\Resources\Integrations;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Vehicle\Gateway;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Integrations\GatewaysResource;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the GatewaysResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class GatewaysResourceTest extends TestCase
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
    public function it_can_create_a_gateway(): void
    {
        $this->http->fake([
            '*/gateways' => $this->http->response([
                'data' => [
                    'id'     => 'gateway-123',
                    'serial' => 'ABC123',
                    'model'  => 'VG34',
                ],
            ], 201),
        ]);

        $resource = new GatewaysResource($this->samsara);

        $gateway = $resource->create([
            'serial' => 'ABC123',
        ]);

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame('ABC123', $gateway->serial);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/gateways'
            && $request->data() === ['serial' => 'ABC123']
        );
    }

    #[Test]
    public function it_can_delete_a_gateway(): void
    {
        $this->http->fake([
            '*/gateways/gateway-123' => $this->http->response([], 204),
        ]);

        $resource = new GatewaysResource($this->samsara);

        $result = $resource->delete('gateway-123');

        $this->assertTrue($result);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/gateways/gateway-123'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_can_get_all_gateways(): void
    {
        $this->http->fake([
            '*/gateways' => $this->http->response([
                'data' => [
                    ['id' => 'gateway-1', 'serial' => 'ABC123'],
                    ['id' => 'gateway-2', 'serial' => 'DEF456'],
                ],
            ], 200),
        ]);

        $resource = new GatewaysResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Gateway::class, $result->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/gateways'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_exposes_a_query_builder_for_cursor_pagination(): void
    {
        $resource = new GatewaysResource($this->samsara);

        $this->assertTrue(method_exists($resource, 'query'));
        $this->assertInstanceOf(Builder::class, $resource->query());
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new GatewaysResource($this->samsara);

        $this->assertSame('/gateways', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new GatewaysResource($this->samsara);

        $this->assertSame(Gateway::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_paginates_gateways_without_an_unsupported_limit(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data' => [[
                    'id'     => 'gateway-1',
                    'serial' => 'ABC123',
                    'model'  => 'VG34',
                ]],
                'pagination' => ['endCursor' => 'gateway-next', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new GatewaysResource($this->samsara))
            ->query()
            ->where('models', 'VG34,VG55')
            ->paginate();
        $page->nextPage();

        $this->assertInstanceOf(Gateway::class, $page->items()->first());
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/gateways'
            && $request->data() === ['models' => 'VG34,VG55']
        );
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/gateways'
            && $request->data() === ['models' => 'VG34,VG55', 'after' => 'gateway-next']
        );
    }

    #[Test]
    public function it_rejects_unsupported_item_lookup_and_update_without_http(): void
    {
        $resource = new GatewaysResource($this->samsara);

        foreach (['find', 'update'] as $operation) {
            try {
                $operation === 'find'
                    ? $resource->find('gateway-123')
                    : $resource->update('gateway-123', ['serial' => 'DEF456']);
                $this->fail("Gateway {$operation} must fail before sending HTTP.");
            } catch (UnsupportedOperationException $exception) {
                $this->assertSame($operation, $exception->getContext()['operation'] ?? null);
            }
        }

        $this->http->assertNothingSent();
    }
}

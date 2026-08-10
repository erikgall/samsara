<?php

namespace Samsara\Tests\Unit\Resources\Dispatch;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Route\Route;
use Illuminate\Http\Client\Request;
use Samsara\Data\Route\RouteAuditLog;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Dispatch\RoutesResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the RoutesResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RoutesResourceTest extends TestCase
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
    public function it_can_be_created(): void
    {
        $resource = new RoutesResource($this->samsara);

        $this->assertInstanceOf(RoutesResource::class, $resource);
    }

    #[Test]
    public function it_can_create_route(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'route-1',
                    'name' => 'Morning Route',
                ],
            ]),
        ]);

        $resource = new RoutesResource($this->samsara);
        $route = $resource->create([
            'name'  => 'Morning Route',
            'stops' => [
                [
                    'addressId'              => 'addr-1',
                    'scheduledDepartureTime' => '2025-01-20T08:00:00Z',
                ],
                [
                    'addressId'            => 'addr-2',
                    'scheduledArrivalTime' => '2025-01-20T10:00:00Z',
                ],
            ],
        ]);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame('Morning Route', $route->name);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/routes'
            && $request->data() === [
                'name'  => 'Morning Route',
                'stops' => [
                    [
                        'addressId'              => 'addr-1',
                        'scheduledDepartureTime' => '2025-01-20T08:00:00Z',
                    ],
                    [
                        'addressId'            => 'addr-2',
                        'scheduledArrivalTime' => '2025-01-20T10:00:00Z',
                    ],
                ],
            ]
        );
    }

    #[Test]
    public function it_can_delete_route(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 204),
        ]);

        $resource = new RoutesResource($this->samsara);
        $result = $resource->delete('route-1');

        $this->assertTrue($result);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/routes/route-1'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_can_find_route_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'route-1',
                    'name' => 'Evening Route',
                ],
            ]),
        ]);

        $resource = new RoutesResource($this->samsara);
        $route = $resource->find('route-1');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame('Evening Route', $route->name);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/routes/route-1'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_can_get_all_routes(): void
    {
        $resource = new RoutesResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Routes all() cannot supply required startTime and endTime.');
        } catch (UnsupportedOperationException) {
            $this->assertTrue(true);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_can_return_audit_logs_query_builder(): void
    {
        $resource = new RoutesResource($this->samsara);
        $query = $resource->auditLogs();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new RoutesResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_update_route(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'route-1',
                    'name' => 'Updated Route',
                ],
            ]),
        ]);

        $resource = new RoutesResource($this->samsara);
        $route = $resource->update('route-1', ['name' => 'Updated Route']);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame('Updated Route', $route->name);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/routes/route-1'
            && $request->data() === ['name' => 'Updated Route']
        );
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new RoutesResource($this->samsara);

        $this->assertSame('/fleet/routes', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new RoutesResource($this->samsara);

        $this->assertSame(Route::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_maps_and_paginates_route_audit_logs_without_a_limit(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data' => [[
                    'changes' => [
                        'before' => ['stops' => [['id' => 'stop-1', 'state' => 'scheduled']]],
                        'after'  => ['stops' => [['id' => 'stop-1', 'state' => 'skipped']]],
                    ],
                    'operation' => 'stop skipped',
                    'route'     => ['id' => 'route-1', 'name' => 'Morning Route'],
                    'source'    => 'driver',
                    'time'      => '2026-08-10T14:30:00Z',
                    'type'      => 'route tracking',
                ]],
                'pagination' => ['endCursor' => 'audit-next', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new RoutesResource($this->samsara))
            ->auditLogs()
            ->expand('route')
            ->paginate();
        $page->nextPage();

        $auditLog = $page->items()->first();
        $this->assertSame(RouteAuditLog::class, $auditLog::class);
        $this->assertSame('route-1', $auditLog->getRoute()->id);
        $this->assertSame('skipped', $auditLog->changes['after']['stops'][0]['state']);
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/routes/audit-logs/feed'
            && $request->data() === ['expand' => 'route']
        );
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/routes/audit-logs/feed'
            && $request->data() === ['after' => 'audit-next', 'expand' => 'route']
        );
    }

    #[Test]
    public function it_terminal_executes_route_list_with_required_times(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'   => 'route-1',
                    'name' => 'Morning Route',
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new RoutesResource($this->samsara))
            ->query()
            ->between('2026-08-10T14:00:00Z', '2026-08-10T15:00:00Z')
            ->where('include', 'routeTrackingDetails')
            ->paginate(50);

        $this->assertInstanceOf(Route::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/routes'
            && $request->data() === [
                'startTime' => '2026-08-10T14:00:00Z',
                'endTime'   => '2026-08-10T15:00:00Z',
                'include'   => 'routeTrackingDetails',
                'limit'     => 50,
            ]
        );
    }
}

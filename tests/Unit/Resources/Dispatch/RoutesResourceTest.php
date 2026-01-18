<?php

namespace Samsara\Tests\Unit\Resources\Dispatch;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Route\Route;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Dispatch\RoutesResource;
use Illuminate\Http\Client\Factory as HttpFactory;

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
                    'type' => 'dynamic',
                ],
            ]),
        ]);

        $resource = new RoutesResource($this->samsara);
        $route = $resource->create([
            'name' => 'Morning Route',
            'type' => 'dynamic',
        ]);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame('Morning Route', $route->name);
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
    }

    #[Test]
    public function it_can_get_all_routes(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'   => 'route-1',
                        'name' => 'Morning Route',
                    ],
                    [
                        'id'   => 'route-2',
                        'name' => 'Afternoon Route',
                    ],
                ],
            ]),
        ]);

        $resource = new RoutesResource($this->samsara);
        $routes = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $routes);
        $this->assertCount(2, $routes);
        $this->assertInstanceOf(Route::class, $routes->first());
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
}

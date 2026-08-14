<?php

namespace Samsara\Tests\Unit\Resources\Industrial;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Asset\Asset;
use Samsara\Data\EntityCollection;
use Samsara\Data\Asset\AssetLocation;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Industrial\AssetsResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the AssetsResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AssetsResourceTest extends TestCase
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
    public function it_can_create_an_asset(): void
    {
        $this->http->fake([
            '*/assets' => $this->http->response([
                'data' => [
                    'id'   => 'asset-123',
                    'name' => 'New Asset',
                ],
            ], 200),
        ]);

        $resource = new AssetsResource($this->samsara);

        $asset = $resource->create(['name' => 'New Asset']);

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame('New Asset', $asset->name);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/assets'
            && $request->data() === ['name' => 'New Asset']);
    }

    #[Test]
    public function it_can_create_query_builder(): void
    {
        $resource = new AssetsResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_delete_an_asset(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 204),
        ]);

        $resource = new AssetsResource($this->samsara);

        $result = $resource->delete('asset-123');

        $this->assertTrue($result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/assets'
            && $this->queryFor($request->url()) === ['id' => 'asset-123']);
    }

    #[Test]
    public function it_can_get_all_assets(): void
    {
        $this->http->fake([
            '*/assets' => $this->http->response([
                'data' => [
                    ['id' => 'asset-1', 'name' => 'Asset 1'],
                    ['id' => 'asset-2', 'name' => 'Asset 2'],
                ],
            ], 200),
        ]);

        $resource = new AssetsResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_legacy_locations(): void
    {
        $this->http->fake([
            '*v1/fleet/assets/locations*' => $this->http->response([
                'assets' => [
                    [
                        'id'       => 1,
                        'name'     => 'Asset 1',
                        'location' => ['latitude' => 37.7749, 'longitude' => -122.4194],
                    ],
                ],
            ], 200),
        ]);

        $resource = new AssetsResource($this->samsara);

        $result = $resource->locations(['groupId' => 123]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('assets', $result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/v1/fleet/assets/locations'
            && $this->queryFor($request->url()) === ['groupId' => '123']);
    }

    #[Test]
    public function it_can_get_legacy_locations_without_optional_query_parameters(): void
    {
        $this->http->fake([
            '*v1/fleet/assets/locations' => $this->http->response([
                'assets'     => [],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $result = (new AssetsResource($this->samsara))->locations();

        $this->assertSame([], $result['assets']);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/v1/fleet/assets/locations');
    }

    #[Test]
    public function it_can_get_legacy_reefers(): void
    {
        $this->http->fake([
            '*v1/fleet/assets/reefers*' => $this->http->response([
                'reeferStats' => [
                    [
                        'assetId'     => 1,
                        'temperature' => -5.0,
                    ],
                ],
            ], 200),
        ]);

        $resource = new AssetsResource($this->samsara);

        $result = $resource->reefers(['startMs' => 1000, 'endMs' => 2000]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('reeferStats', $result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/v1/fleet/assets/reefers'
            && $this->queryFor($request->url()) === ['startMs' => '1000', 'endMs' => '2000']);
    }

    #[Test]
    public function it_can_update_an_asset(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'asset-123',
                    'name' => 'Updated Asset',
                ],
            ], 200),
        ]);

        $resource = new AssetsResource($this->samsara);

        $asset = $resource->update('asset-123', ['name' => 'Updated Asset']);

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame('Updated Asset', $asset->name);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/assets'
            && $this->queryFor($request->url()) === ['id' => 'asset-123']
            && $request->data() === ['name' => 'Updated Asset']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new AssetsResource($this->samsara);

        $this->assertSame('/assets', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new AssetsResource($this->samsara);

        $this->assertSame(Asset::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_maps_asset_inputs_to_a_generic_entity_without_an_implicit_limit(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'input-1', 'name' => 'Engine state']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $resource = new AssetsResource($this->samsara);

        $result = $resource->inputsStream()->after('cursor-1')->paginate();

        $this->assertInstanceOf(Entity::class, $result->items()->first());
        $this->assertNotInstanceOf(Asset::class, $result->items()->first());
        $this->http->assertSent(fn ($request): bool => parse_url($request->url(), PHP_URL_PATH) === '/assets/inputs/stream'
            && $this->queryFor($request->url()) === ['after' => 'cursor-1']);
    }

    #[Test]
    public function it_maps_location_and_speed_items_to_asset_locations(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'asset'    => ['id' => 'asset-1'],
                    'location' => ['latitude' => 35.8, 'longitude' => -86.4],
                ]],
            ]),
        ]);
        $resource = new AssetsResource($this->samsara);

        $result = $resource->locationAndSpeedStream()->get();

        $this->assertInstanceOf(AssetLocation::class, $result->first());
        $this->assertSame(35.8, $result->first()->getCoordinates()['latitude']);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/assets/location-and-speed/stream');
    }

    #[Test]
    public function it_paginates_legacy_locations_from_the_assets_collection(): void
    {
        $this->http->fakeSequence()
            ->push([
                'assets'     => [['id' => 'asset-1', 'name' => 'Asset 1']],
                'pagination' => ['endCursor' => 'locations-next', 'hasNextPage' => true],
            ])
            ->push([
                'assets'     => [['id' => 'asset-2', 'name' => 'Asset 2']],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new AssetsResource($this->samsara))->locationsQuery()->paginate(1);
        $nextPage = $page->nextPage();

        $this->assertInstanceOf(Entity::class, $page->items()->first());
        $this->assertNotInstanceOf(Asset::class, $page->items()->first());
        $this->assertSame('asset-1', $page->items()->first()->getId());
        $this->assertSame('asset-2', $nextPage?->items()->first()->getId());
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/v1/fleet/assets/locations'
            && $this->queryFor($request->url()) === ['limit' => '1']);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/v1/fleet/assets/locations'
            && $this->queryFor($request->url()) === ['limit' => '1', 'startingAfter' => 'locations-next']);
    }

    #[Test]
    public function it_paginates_legacy_reefers_from_the_data_collection(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data'       => [['id' => 'reefer-1', 'assetId' => 'asset-1']],
                'pagination' => ['endCursor' => 'reefers-next', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [['id' => 'reefer-2', 'assetId' => 'asset-2']],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new AssetsResource($this->samsara))
            ->reefersQuery()
            ->where('startMs', 1000)
            ->where('endMs', 2000)
            ->paginate(1);
        $nextPage = $page->nextPage();

        $this->assertSame('reefer-1', $page->items()->first()->getId());
        $this->assertSame('reefer-2', $nextPage?->items()->first()->getId());
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/v1/fleet/assets/reefers'
            && $this->queryFor($request->url()) === [
                'startMs' => '1000',
                'endMs'   => '2000',
                'limit'   => '1',
            ]);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/v1/fleet/assets/reefers'
            && $this->queryFor($request->url()) === [
                'startMs'       => '1000',
                'endMs'         => '2000',
                'limit'         => '1',
                'startingAfter' => 'reefers-next',
            ]);
    }

    #[Test]
    public function it_rejects_the_phantom_generic_find_operation(): void
    {
        $this->http->fake();

        $resource = new AssetsResource($this->samsara);

        try {
            $resource->find('asset-123');
            $this->fail('Expected the inherited find operation to be rejected.');
        } catch (UnsupportedOperationException) {
            $this->http->assertNothingSent();
        }
    }

    #[Test]
    public function it_repeats_exploded_asset_query_parameters(): void
    {
        $this->http->fake(['*' => $this->http->response(['data' => []])]);
        $resource = new AssetsResource($this->samsara);

        $resource->query()->where('attributes', ['Length:range(8,)', 'Date:range(2025-01-01,)'])->get();
        $resource->depreciation()->where('assetIds', ['asset-1', 'asset-2'])->get();

        $this->http->assertSent(fn ($request): bool => $request->url()
            === 'https://api.samsara.com/assets?attributes=Length%3Arange%288%2C%29&attributes=Date%3Arange%282025-01-01%2C%29');
        $this->http->assertSent(fn ($request): bool => $request->url()
            === 'https://api.samsara.com/assets/depreciation?assetIds=asset-1&assetIds=asset-2');
    }

    #[Test]
    public function it_uses_the_current_depreciation_path_without_an_implicit_limit(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'transaction-1', 'assetId' => 'asset-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $resource = new AssetsResource($this->samsara);

        $result = $resource->depreciation()
            ->where('assetIds', 'asset-1')
            ->after('cursor-1')
            ->paginate();

        $this->assertInstanceOf(Entity::class, $result->items()->first());
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/assets/depreciation'
            && $this->queryFor($request->url()) === [
                'assetIds' => 'asset-1',
                'after'    => 'cursor-1',
            ]);
    }

    #[Test]
    public function its_query_does_not_add_a_limit_the_contract_does_not_support(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'asset-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $resource = new AssetsResource($this->samsara);

        $result = $resource->query()->after('cursor-1')->paginate();

        $this->assertInstanceOf(Asset::class, $result->items()->first());
        $this->http->assertSent(fn ($request): bool => parse_url($request->url(), PHP_URL_PATH) === '/assets'
            && $this->queryFor($request->url()) === ['after' => 'cursor-1']);
    }

    /**
     * @return array<string, string>
     */
    private function queryFor(string $url): array
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        /** @var array<string, string> $query */
        return $query;
    }
}

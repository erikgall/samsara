<?php

namespace Samsara\Tests\Unit\Resources\Industrial;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\DataInput;
use Samsara\Data\Industrial\DataPoint;
use Samsara\Data\Industrial\IndustrialAsset;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Industrial\IndustrialResource;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the IndustrialResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IndustrialResourceTest extends TestCase
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
            '*/industrial/assets' => $this->http->response([
                'data' => [
                    'id'   => 'asset-123',
                    'name' => 'Test Asset',
                ],
            ], 200),
        ]);

        $resource = new IndustrialResource($this->samsara);

        $asset = $resource->createAsset([
            'name' => 'Test Asset',
        ]);

        $this->assertInstanceOf(IndustrialAsset::class, $asset);
        $this->assertSame('asset-123', $asset->id);
        $this->assertSame('Test Asset', $asset->name);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/industrial/assets'
            && $request->data() === ['name' => 'Test Asset']);
    }

    #[Test]
    public function it_can_delete_an_asset(): void
    {
        $this->http->fake([
            '*/industrial/assets/asset-123' => $this->http->response([], 204),
        ]);

        $resource = new IndustrialResource($this->samsara);

        $result = $resource->deleteAsset('asset-123');

        $this->assertTrue($result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/industrial/assets/asset-123');
    }

    #[Test]
    public function it_can_get_assets_builder(): void
    {
        $resource = new IndustrialResource($this->samsara);

        $builder = $resource->assets();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_data_inputs_builder(): void
    {
        $resource = new IndustrialResource($this->samsara);

        $builder = $resource->dataInputs();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_data_points_builder(): void
    {
        $resource = new IndustrialResource($this->samsara);

        $builder = $resource->dataPoints();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_data_points_feed_builder(): void
    {
        $resource = new IndustrialResource($this->samsara);

        $builder = $resource->dataPointsFeed();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_data_points_history_builder(): void
    {
        $resource = new IndustrialResource($this->samsara);

        $builder = $resource->dataPointsHistory();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_an_asset(): void
    {
        $this->http->fake([
            '*/industrial/assets/asset-123' => $this->http->response([
                'data' => [
                    'id'   => 'asset-123',
                    'name' => 'Updated Asset',
                ],
            ], 200),
        ]);

        $resource = new IndustrialResource($this->samsara);

        $asset = $resource->updateAsset('asset-123', [
            'name' => 'Updated Asset',
        ]);

        $this->assertInstanceOf(IndustrialAsset::class, $asset);
        $this->assertSame('Updated Asset', $asset->name);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/industrial/assets/asset-123'
            && $request->data() === ['name' => 'Updated Asset']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new IndustrialResource($this->samsara);

        $this->assertSame('/industrial/assets', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new IndustrialResource($this->samsara);

        $this->assertSame(IndustrialAsset::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_maps_data_inputs_to_data_input_entities(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'input-1', 'name' => 'Temperature']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $resource = new IndustrialResource($this->samsara);

        $result = $resource->dataInputs()->whereTag(['tag-1', 'tag-2'])->paginate(10);

        $this->assertInstanceOf(DataInput::class, $result->items()->first());
        $this->http->assertSent(fn ($request): bool => parse_url($request->url(), PHP_URL_PATH) === '/industrial/data-inputs'
            && $this->queryFor($request->url()) === [
                'tagIds' => 'tag-1,tag-2',
                'limit'  => '10',
            ]);
    }

    #[Test]
    public function it_rejects_the_phantom_generic_find_operation(): void
    {
        $this->http->fake();
        $resource = new IndustrialResource($this->samsara);

        try {
            $resource->find('asset-123');
            $this->fail('Expected the inherited find operation to be rejected.');
        } catch (UnsupportedOperationException) {
            $this->http->assertNothingSent();
        }
    }

    #[Test]
    public function it_serializes_industrial_asset_filters_and_maps_the_owned_entity(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'asset-123', 'name' => 'Mixer']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $resource = new IndustrialResource($this->samsara);

        $result = $resource->assets()
            ->whereParentTag(['parent-1', 'parent-2'])
            ->whereTag(['tag-1', 'tag-2'])
            ->paginate(25);

        $this->assertInstanceOf(IndustrialAsset::class, $result->items()->first());
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/industrial/assets'
            && $this->queryFor($request->url()) === [
                'parentTagIds' => 'parent-1,parent-2',
                'tagIds'       => 'tag-1,tag-2',
                'limit'        => '25',
            ]);
    }

    #[Test]
    public function it_uses_current_data_point_paths_and_hydrates_nested_points(): void
    {
        $builders = [
            'dataPoints'        => '/industrial/data-inputs/data-points',
            'dataPointsFeed'    => '/industrial/data-inputs/data-points/feed',
            'dataPointsHistory' => '/industrial/data-inputs/data-points/history',
        ];

        foreach ($builders as $method => $path) {
            $this->http->fake([
                '*' => $this->http->response([
                    'data' => [[
                        'id'          => 'input-1',
                        'numberPoint' => ['time' => '2026-08-10T12:00:00Z', 'value' => 42.5],
                    ]],
                    'pagination' => ['hasNextPage' => false],
                ]),
            ]);
            $resource = new IndustrialResource($this->samsara);

            $result = $resource->{$method}()->after('cursor-1')->paginate();

            $this->assertInstanceOf(DataInput::class, $result->items()->first());
            $this->assertInstanceOf(DataPoint::class, $result->items()->first()->numberPoint);
            $this->http->assertSent(fn ($request): bool => parse_url($request->url(), PHP_URL_PATH) === $path
                && $this->queryFor($request->url()) === ['after' => 'cursor-1']);
            $this->http->preventStrayRequests();
        }
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

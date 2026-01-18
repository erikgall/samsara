<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Industrial;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use ErikGall\Samsara\Data\Asset\Asset;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Industrial\AssetsResource;

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
            ], 201),
        ]);

        $resource = new AssetsResource($this->samsara);

        $asset = $resource->create(['name' => 'New Asset']);

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame('New Asset', $asset->name);
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
            '*/assets/asset-123' => $this->http->response([], 204),
        ]);

        $resource = new AssetsResource($this->samsara);

        $result = $resource->delete('asset-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_an_asset(): void
    {
        $this->http->fake([
            '*/assets/asset-123' => $this->http->response([
                'data' => [
                    'id'   => 'asset-123',
                    'name' => 'Test Asset',
                ],
            ], 200),
        ]);

        $resource = new AssetsResource($this->samsara);

        $asset = $resource->find('asset-123');

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame('asset-123', $asset->id);
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
    public function it_can_get_depreciation_builder(): void
    {
        $resource = new AssetsResource($this->samsara);

        $builder = $resource->depreciation();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_inputs_stream_builder(): void
    {
        $resource = new AssetsResource($this->samsara);

        $builder = $resource->inputsStream();

        $this->assertInstanceOf(Builder::class, $builder);
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

        $result = $resource->reefers(['groupId' => 123]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('reeferStats', $result);
    }

    #[Test]
    public function it_can_get_location_and_speed_stream_builder(): void
    {
        $resource = new AssetsResource($this->samsara);

        $builder = $resource->locationAndSpeedStream();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_an_asset(): void
    {
        $this->http->fake([
            '*/assets/asset-123' => $this->http->response([
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
}

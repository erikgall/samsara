<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Industrial;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Data\Industrial\IndustrialAsset;
use ErikGall\Samsara\Resources\Industrial\IndustrialResource;

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
            ], 201),
        ]);

        $resource = new IndustrialResource($this->samsara);

        $asset = $resource->createAsset([
            'name' => 'Test Asset',
        ]);

        $this->assertInstanceOf(IndustrialAsset::class, $asset);
        $this->assertSame('asset-123', $asset->id);
        $this->assertSame('Test Asset', $asset->name);
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
}

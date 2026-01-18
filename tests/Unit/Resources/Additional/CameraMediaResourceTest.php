<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\CameraMediaResource;

/**
 * Unit tests for the CameraMediaResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CameraMediaResourceTest extends TestCase
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
    public function it_can_get_camera_media(): void
    {
        $this->http->fake([
            '*/camera-media' => $this->http->response([
                'data' => [
                    ['id' => 'media-1'],
                    ['id' => 'media-2'],
                ],
            ], 200),
        ]);

        $resource = new CameraMediaResource($this->samsara);

        $result = $resource->get();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_retrieval_status(): void
    {
        $this->http->fake([
            '*/camera-media/retrieve/retrieval-123' => $this->http->response([
                'data' => [
                    'id'     => 'retrieval-123',
                    'status' => 'completed',
                    'url'    => 'https://example.com/video.mp4',
                ],
            ], 200),
        ]);

        $resource = new CameraMediaResource($this->samsara);

        $result = $resource->getRetrieval('retrieval-123');

        $this->assertIsArray($result);
        $this->assertSame('completed', $result['status']);
    }

    #[Test]
    public function it_can_request_media_retrieval(): void
    {
        $this->http->fake([
            '*/camera-media/retrieve' => $this->http->response([
                'data' => [
                    'id'     => 'retrieval-123',
                    'status' => 'pending',
                ],
            ], 201),
        ]);

        $resource = new CameraMediaResource($this->samsara);

        $result = $resource->retrieve([
            'vehicleId' => 'vehicle-1',
            'startTime' => '2024-01-01T00:00:00Z',
            'endTime'   => '2024-01-01T01:00:00Z',
        ]);

        $this->assertIsArray($result);
        $this->assertSame('retrieval-123', $result['id']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new CameraMediaResource($this->samsara);

        $this->assertSame('/camera-media', $resource->getEndpoint());
    }
}

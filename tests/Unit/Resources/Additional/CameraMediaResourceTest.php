<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
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
            '*/cameras/media*' => $this->http->response([
                'data' => ['media' => [
                    ['id' => 'media-1'],
                    ['id' => 'media-2'],
                ]],
                'pagination' => ['hasNextPage' => false],
            ], 200),
        ]);

        $resource = new CameraMediaResource($this->samsara);

        $result = $resource->get([
            'vehicleIds' => 'vehicle-1',
            'startTime'  => '2024-01-01T00:00:00Z',
            'endTime'    => '2024-01-01T01:00:00Z',
        ]);

        $this->assertCount(2, $result['data']['media']);
        $this->assertFalse($result['pagination']['hasNextPage']);
    }

    #[Test]
    public function it_can_get_retrieval_status(): void
    {
        $this->http->fake([
            '*/cameras/media/retrieval*' => $this->http->response([
                'data' => [
                    'media' => [['id' => 'media-1']],
                ],
            ], 200),
        ]);

        $resource = new CameraMediaResource($this->samsara);

        $result = $resource->getRetrieval('retrieval-123');

        $this->assertIsArray($result);
        $this->assertSame('media-1', $result['media'][0]['id']);
    }

    #[Test]
    public function it_can_request_media_retrieval(): void
    {
        $this->http->fake([
            '*/cameras/media/retrieval' => $this->http->response([
                'data' => [
                    'retrievalId' => 'retrieval-123',
                    'quotaStatus' => 'available',
                ],
            ], 200),
        ]);

        $resource = new CameraMediaResource($this->samsara);

        $result = $resource->retrieve([
            'vehicleId' => 'vehicle-1',
            'startTime' => '2024-01-01T00:00:00Z',
            'endTime'   => '2024-01-01T01:00:00Z',
            'mediaType' => 'videoHighRes',
        ]);

        $this->assertIsArray($result);
        $this->assertSame('retrieval-123', $result['retrievalId']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new CameraMediaResource($this->samsara);

        $this->assertSame('/cameras/media', $resource->getEndpoint());
    }
}

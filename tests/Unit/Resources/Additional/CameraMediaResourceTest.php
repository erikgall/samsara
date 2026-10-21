<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_get_retrieval_status(): void
    {
        $this->http->fake([
            '*/cameras/media/retrieval*' => $this->http->response([
                'data' => [
                    'media' => [[
                        'vehicleId' => 'vehicle-1',
                        'startTime' => '2024-01-01T00:00:00Z',
                        'endTime'   => '2024-01-01T01:00:00Z',
                        'input'     => 'dashcamRoadFacing',
                        'mediaType' => 'videoHighRes',
                        'status'    => 'available',
                        'urlInfo'   => [
                            'url'           => 'https://example.test/media.mp4',
                            'urlExpiryTime' => '2024-01-01T09:00:00Z',
                        ],
                    ]],
                ],
            ], 200),
        ]);

        $resource = new CameraMediaResource($this->samsara);

        $result = $resource->getRetrieval('retrieval-123');

        $this->assertIsArray($result);
        $this->assertSame('available', $result['media'][0]['status']);
        $this->assertSame('https://example.test/media.mp4', $result['media'][0]['urlInfo']['url']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/cameras/media/retrieval'
            && str_contains($request->url(), 'retrievalId=retrieval-123'));
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

        $payload = [
            'vehicleId' => 'vehicle-1',
            'startTime' => '2024-01-01T00:00:00Z',
            'endTime'   => '2024-01-01T01:00:00Z',
            'mediaType' => 'videoHighRes',
        ];
        $result = $resource->retrieve($payload);

        $this->assertIsArray($result);
        $this->assertSame('retrieval-123', $result['retrievalId']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/cameras/media/retrieval'
            && $request->data() === $payload);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new CameraMediaResource($this->samsara);

        $this->assertSame('/cameras/media', $resource->getEndpoint());
    }

    #[Test]
    public function it_paginates_the_nested_media_collection_without_an_unsupported_limit(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data'       => ['media' => [['id' => 'media-1']]],
                'pagination' => ['endCursor' => 'media-next', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => ['media' => [['id' => 'media-2']]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new CameraMediaResource($this->samsara))
            ->query()
            ->where('vehicleIds', 'vehicle-1')
            ->paginate();
        $nextPage = $page->nextPage();

        $this->assertInstanceOf(Entity::class, $page->items()->first());
        $this->assertSame('media-1', $page->items()->first()->getId());
        $this->assertSame('media-2', $nextPage?->items()->first()->getId());
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/cameras/media'
            && $request->data() === ['vehicleIds' => 'vehicle-1']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/cameras/media'
            && $request->data() === ['vehicleIds' => 'vehicle-1', 'after' => 'media-next']);
    }
}

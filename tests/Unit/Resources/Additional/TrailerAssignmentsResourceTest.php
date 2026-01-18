<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Additional\TrailerAssignmentsResource;

/**
 * Unit tests for the TrailerAssignmentsResource class (Legacy).
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TrailerAssignmentsResourceTest extends TestCase
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
    public function it_can_get_all_assignments(): void
    {
        $this->http->fake([
            '*/v1/fleet/trailers/assignments' => $this->http->response([
                'data' => [
                    ['id' => 'assignment-1'],
                    ['id' => 'assignment-2'],
                ],
            ], 200),
        ]);

        $resource = new TrailerAssignmentsResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_assignments_for_trailer(): void
    {
        $this->http->fake([
            '*v1/fleet/trailers/trailer-123/assignments*' => $this->http->response([
                'data' => [
                    ['id' => 'assignment-1', 'trailerId' => 'trailer-123'],
                ],
            ], 200),
        ]);

        $resource = new TrailerAssignmentsResource($this->samsara);

        $result = $resource->forTrailer('trailer-123');

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(1, $result);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new TrailerAssignmentsResource($this->samsara);

        $this->assertSame('/v1/fleet/trailers/assignments', $resource->getEndpoint());
    }
}

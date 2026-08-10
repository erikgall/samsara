<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\Trailer\Trailer;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;
use Samsara\Resources\Additional\TrailerAssignmentsResource;

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
    public function it_can_get_assignments_for_trailer(): void
    {
        $this->http->fake([
            '*v1/fleet/trailers/123/assignments*' => $this->http->response([
                'id'                 => 123,
                'name'               => 'Trailer 123',
                'trailerAssignments' => [
                    ['driverId' => 456],
                ],
            ], 200),
        ]);

        $resource = new TrailerAssignmentsResource($this->samsara);

        $result = $resource->forTrailer(123);

        $this->assertInstanceOf(Trailer::class, $result);
        $this->assertSame(123, $result->get('id'));
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new TrailerAssignmentsResource($this->samsara);

        $this->assertSame('/v1/fleet/trailers/assignments', $resource->getEndpoint());
    }

    #[Test]
    public function it_rejects_generic_all_for_the_wrapped_collection_shape(): void
    {
        $this->http->fake();

        $resource = new TrailerAssignmentsResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the generic all operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('all', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }
}

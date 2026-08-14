<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Trailer\Trailer;
use Illuminate\Http\Client\Request;
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
    public function it_queries_the_wrapped_trailer_assignment_collection(): void
    {
        $this->http->fake(['*' => $this->http->response([
            'trailers' => [
                ['id' => 'trailer-1', 'name' => 'Trailer 1'],
                ['id' => 'trailer-2', 'name' => 'Trailer 2'],
            ],
            'pagination' => [],
        ])]);
        $resource = new TrailerAssignmentsResource($this->samsara);

        $query = $resource->query();
        $assignments = $query->where('startMs', 1000)->get();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertCount(2, $assignments);
        $this->assertInstanceOf(Trailer::class, $assignments->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/v1/fleet/trailers/assignments'
            && parse_url($request->url(), PHP_URL_QUERY) === 'startMs=1000');
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

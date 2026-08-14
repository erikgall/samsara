<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;
use Samsara\Resources\Additional\DriverTrailerAssignmentsResource;

/**
 * Unit tests for the DriverTrailerAssignmentsResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriverTrailerAssignmentsResourceTest extends TestCase
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
    public function it_can_create_assignment(): void
    {
        $this->http->fake([
            '*/driver-trailer-assignments' => $this->http->response([
                'data' => [
                    'id' => 'assignment-123',
                ],
            ], 201),
        ]);

        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $result = $resource->create(['driverId' => 'driver-1', 'trailerId' => 'trailer-1']);

        $this->assertInstanceOf(Entity::class, $result);
    }

    #[Test]
    public function it_can_get_query_builder(): void
    {
        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $this->assertSame('/driver-trailer-assignments', $resource->getEndpoint());
    }

    #[Test]
    public function it_rejects_the_deferred_generic_update_without_sending_a_request(): void
    {
        $this->http->fake();

        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        try {
            $resource->update('assignment-123', ['trailerId' => 'trailer-2']);
            $this->fail('Expected the generic update operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('update', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_rejects_unfiltered_generic_all_without_sending_a_request(): void
    {
        $this->http->fake();

        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the generic all operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('all', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_updates_an_assignment_with_the_contract_query_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => [
                'id'        => 'assignment-123',
                'driverId'  => 'driver-1',
                'trailerId' => 'trailer-1',
                'startTime' => '2026-08-14T12:00:00Z',
                'endTime'   => '2026-08-14T13:00:00Z',
            ]]),
        ]);
        $payload = ['endTime' => '2026-08-14T13:00:00Z'];

        $assignment = (new DriverTrailerAssignmentsResource($this->samsara))
            ->updateAssignment('assignment/123', $payload);

        $this->assertInstanceOf(Entity::class, $assignment);
        $this->assertSame('assignment-123', $assignment->id);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/driver-trailer-assignments'
            && $this->queryFor($request->url()) === ['id' => 'assignment/123']
            && $request->data() === $payload);
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

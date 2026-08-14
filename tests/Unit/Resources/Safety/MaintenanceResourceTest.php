<?php

namespace Samsara\Tests\Unit\Resources\Safety;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Maintenance\Dvir;
use Illuminate\Http\Client\Request;
use Samsara\Data\Maintenance\Defect;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Maintenance\DefectType;
use Samsara\Resources\Safety\MaintenanceResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the MaintenanceResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class MaintenanceResourceTest extends TestCase
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
    public function it_can_be_created(): void
    {
        $resource = new MaintenanceResource($this->samsara);

        $this->assertInstanceOf(MaintenanceResource::class, $resource);
    }

    #[Test]
    public function it_can_create_dvir(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'           => 'dvir-1',
                    'type'         => 'mechanic',
                    'safetyStatus' => 'safe',
                ],
            ]),
        ]);

        $resource = new HydrationAwareMaintenanceResource($this->samsara);
        $dvir = $resource->createDvir([
            'authorId'     => 'author-123',
            'safetyStatus' => 'safe',
            'type'         => 'mechanic',
            'vehicleId'    => 'vehicle-123',
        ]);

        $this->assertInstanceOf(Dvir::class, $dvir);
        $this->assertSame('mechanic', $dvir->type);
        $this->assertTrue($resource->mappedCreateResponse);
    }

    #[Test]
    public function it_can_return_defects_query_builder(): void
    {
        $resource = new MaintenanceResource($this->samsara);
        $query = $resource->defects();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_dvirs_query_builder(): void
    {
        $resource = new MaintenanceResource($this->samsara);
        $query = $resource->dvirs();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new MaintenanceResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_does_not_hydrate_a_dvir_from_the_whole_payload(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['id' => 'wrong-level-dvir']),
        ]);

        $dvir = (new MaintenanceResource($this->samsara))->createDvir([]);

        $this->assertNull($dvir->id);
    }

    #[Test]
    public function it_finds_a_defect_at_the_current_item_endpoint(): void
    {
        $this->http->fake(['*' => $this->http->response(['id' => 'defect-1'])]);

        $defect = (new MaintenanceResource($this->samsara))->findDefect('defect/1');

        $this->assertInstanceOf(Defect::class, $defect);
        $this->assertSame('defect-1', $defect->id);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/defects/defect%2F1');
    }

    #[Test]
    public function it_finds_a_dvir_at_the_current_item_endpoint(): void
    {
        $this->http->fake(['*' => $this->http->response(['id' => 'dvir-1'])]);

        $dvir = (new MaintenanceResource($this->samsara))->findDvir(
            'dvir/1',
            ['includeExternalIds' => true],
        );

        $this->assertInstanceOf(Dvir::class, $dvir);
        $this->assertSame('dvir-1', $dvir->id);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/dvirs/dvir%2F1'
            && $this->queryFor($request->url()) === ['includeExternalIds' => 'true']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new MaintenanceResource($this->samsara);

        $this->assertSame('/dvirs/stream', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new MaintenanceResource($this->samsara);

        $this->assertSame(Dvir::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_maps_defect_types_from_the_current_collection_endpoint(): void
    {
        $this->http->fake(['*' => $this->http->response([
            'data'       => [['id' => 'type-1', 'label' => 'Brakes']],
            'pagination' => ['hasNextPage' => false],
        ])]);

        $types = (new MaintenanceResource($this->samsara))
            ->defectTypes()
            ->where('ids', 'type-1,type-2')
            ->get();

        $defectType = $types->first();

        $this->assertInstanceOf(DefectType::class, $defectType);
        $this->assertSame('Brakes', $defectType->label);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/defect-types'
            && $this->queryFor($request->url()) === ['ids' => 'type-1,type-2']);
    }

    #[Test]
    public function it_rejects_the_unsupported_generic_all_operation_before_http(): void
    {
        $this->http->fake();

        $resource = new MaintenanceResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the generic all operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('all', $exception->getContext()['operation']);
            $this->assertSame(MaintenanceResource::class, $exception->getContext()['resource']);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_resolves_a_dvir_at_the_fleet_item_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => [
                'id'           => 'dvir-1',
                'safetyStatus' => 'resolved',
            ]]),
        ]);
        $payload = ['authorId' => 'user-1', 'isResolved' => true];

        $dvir = (new MaintenanceResource($this->samsara))->resolveDvir('dvir/1', $payload);

        $this->assertInstanceOf(Dvir::class, $dvir);
        $this->assertSame('resolved', $dvir->safetyStatus);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/dvirs/dvir%2F1'
            && $request->data() === $payload);
    }

    #[Test]
    public function it_updates_a_defect_at_the_fleet_item_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => [
                'id'         => 'defect-1',
                'isResolved' => true,
            ]]),
        ]);
        $payload = ['isResolved' => true];

        $defect = (new MaintenanceResource($this->samsara))->updateDefect('defect/1', $payload);

        $this->assertInstanceOf(Defect::class, $defect);
        $this->assertTrue($defect->isResolved);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/defects/defect%2F1'
            && $request->data() === $payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function queryFor(string $url): array
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        /** @var array<string, mixed> $query */
        return $query;
    }
}

class HydrationAwareMaintenanceResource extends MaintenanceResource
{
    public bool $mappedCreateResponse = false;

    protected function mapToEntity(array $data): object
    {
        $this->mappedCreateResponse = true;

        return parent::mapToEntity($data);
    }
}

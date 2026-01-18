<?php

namespace Samsara\Tests\Unit\Resources\Fleet;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Driver\Driver;
use Illuminate\Support\Collection;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Fleet\DriversResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the DriversResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriversResourceTest extends TestCase
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
    public function it_can_activate_driver(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'                     => '123',
                    'name'                   => 'John Smith',
                    'driverActivationStatus' => 'active',
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->activate('123');

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('active', $driver->driverActivationStatus);
    }

    #[Test]
    public function it_can_be_created(): void
    {
        $resource = new DriversResource($this->samsara);

        $this->assertInstanceOf(DriversResource::class, $resource);
    }

    #[Test]
    public function it_can_create_auth_token(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'token' => 'abc123xyz',
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $token = $resource->createAuthToken('123');

        $this->assertSame('abc123xyz', $token);
    }

    #[Test]
    public function it_can_create_driver(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => '123',
                    'name' => 'New Driver',
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->create([
            'name'     => 'New Driver',
            'username' => 'newdriver',
        ]);

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('123', $driver->id);
        $this->assertSame('New Driver', $driver->name);
    }

    #[Test]
    public function it_can_create_qr_code(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'qr-123',
                    'name' => 'New QR Code',
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $qrCode = $resource->createQrCode(['name' => 'New QR Code']);

        $this->assertIsObject($qrCode);
        $this->assertSame('qr-123', $qrCode->id);
    }

    #[Test]
    public function it_can_deactivate_driver(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'                     => '123',
                    'name'                   => 'John Smith',
                    'driverActivationStatus' => 'deactivated',
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->deactivate('123');

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('deactivated', $driver->driverActivationStatus);
    }

    #[Test]
    public function it_can_delete_qr_code(): void
    {
        $this->http->fake([
            '*' => $this->http->response([]),
        ]);

        $resource = new DriversResource($this->samsara);
        $result = $resource->deleteQrCode('qr-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_filter_active_drivers(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'name' => 'Active Driver', 'driverActivationStatus' => 'active'],
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $drivers = $resource->active()->get();

        $this->assertInstanceOf(EntityCollection::class, $drivers);
        $this->assertCount(1, $drivers);
    }

    #[Test]
    public function it_can_filter_deactivated_drivers(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'name' => 'Inactive Driver', 'driverActivationStatus' => 'deactivated'],
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $drivers = $resource->deactivated()->get();

        $this->assertInstanceOf(EntityCollection::class, $drivers);
        $this->assertCount(1, $drivers);
    }

    #[Test]
    public function it_can_find_driver_by_external_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'          => '123',
                        'name'        => 'John Smith',
                        'externalIds' => ['payroll' => 'PAY-123'],
                    ],
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->findByExternalId('payroll', 'PAY-123');

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('123', $driver->id);
    }

    #[Test]
    public function it_can_find_driver_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => ['id' => '123', 'name' => 'John Smith'],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->find('123');

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('123', $driver->id);
        $this->assertSame('John Smith', $driver->name);
    }

    #[Test]
    public function it_can_get_all_drivers(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'name' => 'John Smith'],
                    ['id' => '2', 'name' => 'Jane Doe'],
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $drivers = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $drivers);
        $this->assertCount(2, $drivers);
        $this->assertInstanceOf(Driver::class, $drivers->first());
        $this->assertSame('John Smith', $drivers->first()->name);
    }

    #[Test]
    public function it_can_get_qr_codes(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => 'qr-1', 'name' => 'QR Code 1'],
                    ['id' => 'qr-2', 'name' => 'QR Code 2'],
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $qrCodes = $resource->getQrCodes();

        $this->assertInstanceOf(Collection::class, $qrCodes);
        $this->assertCount(2, $qrCodes);
    }

    #[Test]
    public function it_can_remote_sign_out_driver(): void
    {
        $this->http->fake([
            '*' => $this->http->response([]),
        ]);

        $resource = new DriversResource($this->samsara);

        // Should not throw an exception
        $resource->remoteSignOut('123');

        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new DriversResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_update_driver(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => '123',
                    'name' => 'Updated Name',
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->update('123', ['name' => 'Updated Name']);

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('Updated Name', $driver->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new DriversResource($this->samsara);

        $this->assertSame('/fleet/drivers', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new DriversResource($this->samsara);

        $this->assertSame(Driver::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_returns_null_when_driver_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 404),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->find('999');

        $this->assertNull($driver);
    }

    #[Test]
    public function it_returns_null_when_external_id_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->findByExternalId('payroll', 'NONEXISTENT');

        $this->assertNull($driver);
    }
}

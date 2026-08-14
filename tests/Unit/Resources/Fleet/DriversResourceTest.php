<?php

namespace Samsara\Tests\Unit\Resources\Fleet;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\Driver\Driver;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use Samsara\Data\Driver\DriverQrCode;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Driver\DriverAuthToken;
use Samsara\Exceptions\SamsaraException;
use Samsara\Resources\Fleet\DriversResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Data\Driver\DriverRemoteSignOutResult;

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
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers/123'
            && $request->data() === ['driverActivationStatus' => 'active']
        );
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
                'data' => [
                    'expirationTime' => 1_786_380_000_000,
                    'token'          => 'abc123xyz',
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        try {
            $token = $resource->createAuthToken([
                'code'     => 'driver-code-1234',
                'driverId' => 123,
            ]);
        } catch (\TypeError) {
            $this->fail('createAuthToken must accept the staged-v2 request body array.');
        }

        $this->assertTrue($token instanceof DriverAuthToken, 'Expected a typed driver auth token.');
        $this->assertSame('abc123xyz', $token->token);
        $this->assertSame(1_786_380_000_000, $token->expirationTime);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers/auth-token'
            && $request->data() === [
                'code'     => 'driver-code-1234',
                'driverId' => 123,
            ]
        );
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
            'password' => 'SecureP@ssw0rd!',
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
                    'driverId'   => 494123,
                    'qrCodeLink' => 'https://cloud.samsara.com/qr_code/qr-123',
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $qrCode = $resource->createQrCode(['driverId' => 494123]);

        $this->assertSame(DriverQrCode::class, $qrCode::class);
        $this->assertSame(494123, $qrCode->driverId);
        $this->assertSame('https://cloud.samsara.com/qr_code/qr-123', $qrCode->qrCodeLink);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/drivers/qr-codes'
            && $request->data() === ['driverId' => 494123]
        );
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
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers/123'
            && $request->data() === ['driverActivationStatus' => 'deactivated']
        );
    }

    #[Test]
    public function it_can_delete_qr_code(): void
    {
        $this->http->fake([
            '*' => $this->http->response([]),
        ]);

        $resource = new DriversResource($this->samsara);
        $result = $resource->deleteQrCode(494123);

        $this->assertTrue($result);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/drivers/qr-codes'
            && $request->data() === ['driverId' => 494123]
        );
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
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers'
            && $request->data() === ['driverActivationStatus' => 'active']
        );
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
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers'
            && $request->data() === ['driverActivationStatus' => 'deactivated']
        );
    }

    #[Test]
    public function it_can_find_driver_by_external_id(): void
    {
        $this->http->fake(fn (Request $request) => $this->http->response([
            'data' => parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers'
                ? [[
                    'id'          => '123',
                    'name'        => 'John Smith',
                    'externalIds' => ['payroll' => 'PAY-123'],
                ]]
                : [
                    'id'          => '123',
                    'name'        => 'John Smith',
                    'externalIds' => ['payroll' => 'PAY-123'],
                ],
        ]));

        $resource = new DriversResource($this->samsara);
        $driver = $resource->findByExternalId('payroll', 'PAY-123');

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('123', $driver->id);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers/payroll:PAY-123'
            && $request->data() === []
        );
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
                    ['driverId' => 494123, 'qrCodeLink' => 'https://cloud.samsara.com/qr_code/qr-1'],
                    ['driverId' => 494124, 'qrCodeLink' => 'https://cloud.samsara.com/qr_code/qr-2'],
                ],
            ]),
        ]);

        $resource = new DriversResource($this->samsara);
        $qrCodes = $resource->getQrCodes([494123, 494124]);

        $this->assertInstanceOf(EntityCollection::class, $qrCodes);
        $this->assertCount(2, $qrCodes);
        $firstQrCode = $qrCodes->first();
        $this->assertSame(DriverQrCode::class, $firstQrCode::class);
        $this->assertSame(494123, $firstQrCode->driverId);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/drivers/qr-codes'
            && $request->data() === ['driverIds' => '494123,494124']
        );
    }

    #[Test]
    public function it_can_remote_sign_out_driver(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['driverName' => 'John Smith']),
        ]);

        $resource = new DriversResource($this->samsara);

        $result = $resource->remoteSignOut('123');

        $this->assertTrue($result instanceof DriverRemoteSignOutResult, 'Expected a typed sign-out result.');
        $this->assertSame('John Smith', $result->driverName);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers/remote-sign-out'
            && $request->data() === ['driverId' => '123']
        );
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
    public function it_does_not_hydrate_auth_token_from_the_whole_payload_when_data_is_missing(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['token' => 'wrong-level-token']),
        ]);

        $token = (new DriversResource($this->samsara))->createAuthToken([]);

        $this->assertNull($token->token);
    }

    #[Test]
    public function it_does_not_hydrate_qr_code_from_the_whole_payload_when_data_is_missing(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['qrCodeLink' => 'https://wrong-level.test/qr']),
        ]);

        $qrCode = (new DriversResource($this->samsara))->createQrCode([]);

        $this->assertFalse($qrCode->hasQrCode());
    }

    #[Test]
    public function it_encodes_external_id_parts_as_one_path_segment(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => ['id' => '123']]),
        ]);

        $resource = new DriversResource($this->samsara);
        $resource->findByExternalId('fleet code', 'north/east');

        $this->http->assertSent(fn (Request $request): bool => parse_url($request->url(), PHP_URL_PATH)
            === '/fleet/drivers/fleet%20code:north%2Feast');
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
    public function it_rejects_non_list_qr_code_data(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => ['driverId' => 494123],
            ]),
        ]);

        $this->expectException(SamsaraException::class);
        $this->expectExceptionMessage('Expected response data to be a JSON list');

        (new DriversResource($this->samsara))->getQrCodes('494123');
    }

    #[Test]
    public function it_returns_no_qr_codes_when_data_is_missing(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $qrCodes = (new DriversResource($this->samsara))->getQrCodes('494123');

        $this->assertCount(0, $qrCodes);
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
            '*' => $this->http->response([], 404),
        ]);

        $resource = new DriversResource($this->samsara);
        $driver = $resource->findByExternalId('payroll', 'NONEXISTENT');

        $this->assertNull($driver);
        $this->http->assertSent(fn (Request $request): bool => parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers/payroll:NONEXISTENT'
        );
    }

    #[Test]
    public function it_terminal_executes_the_current_driver_list_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'driver-1', 'name' => 'Ada Driver']],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new DriversResource($this->samsara))
            ->query()
            ->where('driverActivationStatus', 'active')
            ->whereParentTag(['parent-1', 'parent-2'])
            ->whereTag(['tag-1', 'tag-2'])
            ->whereAttribute(['attribute-1', 'attribute-2'])
            ->where('attributes', 'driver:licenseClass')
            ->updatedAfter('2026-08-10T14:00:00Z')
            ->createdAfter('2026-08-09T14:00:00Z')
            ->after('driver-cursor')
            ->paginate(50);

        $this->assertInstanceOf(Driver::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/drivers'
            && $request->data() === [
                'driverActivationStatus' => 'active',
                'parentTagIds'           => 'parent-1,parent-2',
                'tagIds'                 => 'tag-1,tag-2',
                'attributeValueIds'      => 'attribute-1,attribute-2',
                'attributes'             => 'driver:licenseClass',
                'updatedAfterTime'       => '2026-08-10T14:00:00Z',
                'createdAfterTime'       => '2026-08-09T14:00:00Z',
                'limit'                  => 50,
                'after'                  => 'driver-cursor',
            ]
        );
    }
}

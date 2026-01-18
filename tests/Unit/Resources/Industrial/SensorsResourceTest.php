<?php

namespace Samsara\Tests\Unit\Resources\Industrial;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Industrial\SensorsResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the SensorsResource class (Legacy v1).
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SensorsResourceTest extends TestCase
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
    public function it_can_get_cargo_data(): void
    {
        $this->http->fake([
            '*/v1/sensors/cargo' => $this->http->response([
                'sensors' => [
                    [
                        'id'         => 1,
                        'name'       => 'Cargo Sensor',
                        'cargoEmpty' => true,
                    ],
                ],
            ], 200),
        ]);

        $resource = new SensorsResource($this->samsara);

        $result = $resource->cargo(['groupId' => 123, 'sensors' => [1]]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('sensors', $result);
    }

    #[Test]
    public function it_can_get_door_data(): void
    {
        $this->http->fake([
            '*/v1/sensors/door' => $this->http->response([
                'sensors' => [
                    [
                        'id'       => 1,
                        'name'     => 'Door Sensor',
                        'doorOpen' => false,
                    ],
                ],
            ], 200),
        ]);

        $resource = new SensorsResource($this->samsara);

        $result = $resource->door(['groupId' => 123, 'sensors' => [1]]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('sensors', $result);
    }

    #[Test]
    public function it_can_get_humidity_data(): void
    {
        $this->http->fake([
            '*/v1/sensors/humidity' => $this->http->response([
                'sensors' => [
                    [
                        'id'       => 1,
                        'name'     => 'Humidity Sensor',
                        'humidity' => 45.5,
                    ],
                ],
            ], 200),
        ]);

        $resource = new SensorsResource($this->samsara);

        $result = $resource->humidity(['groupId' => 123, 'sensors' => [1]]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('sensors', $result);
    }

    #[Test]
    public function it_can_get_sensor_history(): void
    {
        $this->http->fake([
            '*/v1/sensors/history' => $this->http->response([
                'results' => [
                    [
                        'timeMs' => 1609459200000,
                        'series' => [25.0, 25.5, 26.0],
                    ],
                ],
            ], 200),
        ]);

        $resource = new SensorsResource($this->samsara);

        $result = $resource->history([
            'groupId' => 123,
            'startMs' => 1609459200000,
            'endMs'   => 1609545600000,
            'series'  => ['ambientTemperature'],
            'sensors' => [1],
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('results', $result);
    }

    #[Test]
    public function it_can_get_temperature_data(): void
    {
        $this->http->fake([
            '*/v1/sensors/temperature' => $this->http->response([
                'sensors' => [
                    [
                        'id'                 => 1,
                        'name'               => 'Temp Sensor',
                        'ambientTemperature' => 22.5,
                    ],
                ],
            ], 200),
        ]);

        $resource = new SensorsResource($this->samsara);

        $result = $resource->temperature(['groupId' => 123, 'sensors' => [1]]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('sensors', $result);
    }

    #[Test]
    public function it_can_list_sensors(): void
    {
        $this->http->fake([
            '*/v1/sensors/list' => $this->http->response([
                'sensors' => [
                    ['id' => 1, 'name' => 'Sensor 1'],
                    ['id' => 2, 'name' => 'Sensor 2'],
                ],
            ], 200),
        ]);

        $resource = new SensorsResource($this->samsara);

        $result = $resource->list(['groupId' => 123]);

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new SensorsResource($this->samsara);

        $this->assertSame('/v1/sensors', $resource->getEndpoint());
    }
}

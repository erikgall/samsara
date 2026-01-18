<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Data\Alert\AlertConfiguration;
use ErikGall\Samsara\Resources\Additional\AlertsResource;

/**
 * Unit tests for the AlertsResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AlertsResourceTest extends TestCase
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
    public function it_can_create_configuration(): void
    {
        $this->http->fake([
            '*/alerts/configurations' => $this->http->response([
                'data' => [
                    'id'   => 'config-123',
                    'name' => 'New Alert',
                ],
            ], 201),
        ]);

        $resource = new AlertsResource($this->samsara);

        $config = $resource->createConfiguration(['name' => 'New Alert']);

        $this->assertInstanceOf(AlertConfiguration::class, $config);
        $this->assertSame('config-123', $config->id);
    }

    #[Test]
    public function it_can_delete_configurations(): void
    {
        $this->http->fake([
            '*/alerts/configurations*' => $this->http->response([], 204),
        ]);

        $resource = new AlertsResource($this->samsara);

        $result = $resource->deleteConfigurations(['config-1', 'config-2']);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_get_configurations_builder(): void
    {
        $resource = new AlertsResource($this->samsara);

        $builder = $resource->configurations();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_incidents_builder(): void
    {
        $resource = new AlertsResource($this->samsara);

        $builder = $resource->incidents();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_configuration(): void
    {
        $this->http->fake([
            '*/alerts/configurations/config-123' => $this->http->response([
                'data' => [
                    'id'   => 'config-123',
                    'name' => 'Updated Alert',
                ],
            ], 200),
        ]);

        $resource = new AlertsResource($this->samsara);

        $config = $resource->updateConfiguration('config-123', ['name' => 'Updated Alert']);

        $this->assertInstanceOf(AlertConfiguration::class, $config);
        $this->assertSame('Updated Alert', $config->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new AlertsResource($this->samsara);

        $this->assertSame('/alerts/configurations', $resource->getEndpoint());
    }
}

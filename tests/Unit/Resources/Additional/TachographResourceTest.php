<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\TachographResource;

/**
 * Unit tests for the TachographResource class (EU Only).
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TachographResourceTest extends TestCase
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
    public function it_can_get_driver_activity_history_builder(): void
    {
        $resource = new TachographResource($this->samsara);

        $builder = $resource->driverActivityHistory();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_driver_files_history_builder(): void
    {
        $resource = new TachographResource($this->samsara);

        $builder = $resource->driverFilesHistory();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_vehicle_files_history_builder(): void
    {
        $resource = new TachographResource($this->samsara);

        $builder = $resource->vehicleFilesHistory();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new TachographResource($this->samsara);

        $this->assertSame('/tachograph', $resource->getEndpoint());
    }
}

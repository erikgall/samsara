<?php

namespace ErikGall\Samsara\Tests\Unit\Facades;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\PendingRequest;
use ErikGall\Samsara\Facades\Samsara as SamsaraFacade;

/**
 * Unit tests for the Samsara facade.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraFacadeTest extends TestCase
{
    #[Test]
    public function it_can_call_client_method(): void
    {
        $this->app->forgetInstance(Samsara::class);

        $client = SamsaraFacade::client();

        $this->assertInstanceOf(PendingRequest::class, $client);
    }

    #[Test]
    public function it_can_call_get_base_url_method(): void
    {
        $this->app->forgetInstance(Samsara::class);

        $this->assertSame('https://api.samsara.com', SamsaraFacade::getBaseUrl());
    }

    #[Test]
    public function it_can_call_has_token_method(): void
    {
        $this->app['config']->set('samsara.api_key', 'test-token');
        $this->app->forgetInstance(Samsara::class);

        $this->assertTrue(SamsaraFacade::hasToken());
    }

    #[Test]
    public function it_can_call_use_eu_endpoint_method(): void
    {
        $this->app->forgetInstance(Samsara::class);

        SamsaraFacade::useEuEndpoint();

        $this->assertSame('https://api.eu.samsara.com', SamsaraFacade::getBaseUrl());
    }

    #[Test]
    public function it_can_call_with_token_method(): void
    {
        $this->app->forgetInstance(Samsara::class);

        SamsaraFacade::withToken('new-token');

        $this->assertTrue(SamsaraFacade::hasToken());
    }

    #[Test]
    public function it_resolves_samsara_instance(): void
    {
        $samsara = SamsaraFacade::getFacadeRoot();

        $this->assertInstanceOf(Samsara::class, $samsara);
    }
}

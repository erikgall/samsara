<?php

namespace ErikGall\Samsara\Tests\Unit;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\SamsaraServiceProvider;

/**
 * Unit tests for the Samsara service provider.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraServiceProviderTest extends TestCase
{
    #[Test]
    public function it_configures_api_key_from_config(): void
    {
        $this->app['config']->set('samsara.api_key', 'my-test-token');

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertTrue($samsara->hasToken());
    }

    #[Test]
    public function it_passes_config_to_samsara_instance(): void
    {
        $this->app['config']->set('samsara.timeout', 60);
        $this->app['config']->set('samsara.retry', 5);
        $this->app['config']->set('samsara.per_page', 50);

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertSame(60, $samsara->getConfig('timeout'));
        $this->assertSame(5, $samsara->getConfig('retry'));
        $this->assertSame(50, $samsara->getConfig('per_page'));
    }

    #[Test]
    public function it_provides_samsara_service(): void
    {
        $provider = new SamsaraServiceProvider($this->app);

        $this->assertContains(Samsara::class, $provider->provides());
        $this->assertContains('samsara', $provider->provides());
    }

    #[Test]
    public function it_registers_samsara_singleton(): void
    {
        $samsara1 = $this->app->make(Samsara::class);
        $samsara2 = $this->app->make(Samsara::class);

        $this->assertInstanceOf(Samsara::class, $samsara1);
        $this->assertSame($samsara1, $samsara2);
    }

    #[Test]
    public function it_registers_samsara_with_alias(): void
    {
        $samsara = $this->app->make('samsara');

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_uses_eu_endpoint_when_region_is_eu(): void
    {
        $this->app['config']->set('samsara.region', 'eu');

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertSame('https://api.eu.samsara.com', $samsara->getBaseUrl());
    }

    #[Test]
    public function it_uses_us_endpoint_when_region_is_us(): void
    {
        $this->app['config']->set('samsara.region', 'us');

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
    }
}

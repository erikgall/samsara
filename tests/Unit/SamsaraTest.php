<?php

namespace ErikGall\Samsara\Tests\Unit;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\PendingRequest;

/**
 * Unit tests for the Samsara main client class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraTest extends TestCase
{
    #[Test]
    public function it_accepts_config_array_in_constructor(): void
    {
        $config = [
            'timeout' => 60,
            'retry'   => 5,
        ];

        $samsara = new Samsara('test-token', $config);

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_can_be_created_using_static_make_method(): void
    {
        $samsara = Samsara::make('test-token');

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_can_be_instantiated_with_token(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_can_be_instantiated_without_token(): void
    {
        $samsara = new Samsara;

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_can_get_config_values(): void
    {
        $config = [
            'timeout'  => 60,
            'retry'    => 5,
            'per_page' => 50,
        ];

        $samsara = new Samsara('test-token', $config);

        $this->assertSame(60, $samsara->getConfig('timeout'));
        $this->assertSame(5, $samsara->getConfig('retry'));
        $this->assertSame(50, $samsara->getConfig('per_page'));
    }

    #[Test]
    public function it_can_set_token_using_with_token_method(): void
    {
        $samsara = new Samsara;
        $result = $samsara->withToken('new-token');

        $this->assertSame($samsara, $result);
        $this->assertTrue($samsara->hasToken());
    }

    #[Test]
    public function it_can_switch_to_eu_endpoint(): void
    {
        $samsara = new Samsara('test-token');
        $result = $samsara->useEuEndpoint();

        $this->assertSame($samsara, $result);
        $this->assertSame('https://api.eu.samsara.com', $samsara->getBaseUrl());
    }

    #[Test]
    public function it_returns_default_value_for_missing_config(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertNull($samsara->getConfig('missing'));
        $this->assertSame('default', $samsara->getConfig('missing', 'default'));
    }

    #[Test]
    public function it_returns_false_when_token_is_not_set(): void
    {
        $samsara = new Samsara;

        $this->assertFalse($samsara->hasToken());
    }

    #[Test]
    public function it_returns_pending_request_from_client_method(): void
    {
        $samsara = new Samsara('test-token');

        $client = $samsara->client();

        $this->assertInstanceOf(PendingRequest::class, $client);
    }

    #[Test]
    public function it_returns_true_when_token_is_set(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertTrue($samsara->hasToken());
    }

    #[Test]
    public function it_uses_us_base_url_by_default(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
    }
}

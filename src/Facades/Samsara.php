<?php

namespace ErikGall\Samsara\Facades;

use Illuminate\Support\Facades\Facade;
use ErikGall\Samsara\Testing\SamsaraFake;
use Illuminate\Http\Client\PendingRequest;
use ErikGall\Samsara\Samsara as SamsaraClient;

/**
 * Samsara facade.
 *
 * @method static SamsaraClient withToken(string $token)
 * @method static bool hasToken()
 * @method static string getBaseUrl()
 * @method static SamsaraClient useEuEndpoint()
 * @method static SamsaraClient useUsEndpoint()
 * @method static PendingRequest client()
 * @method static mixed getConfig(string $key, mixed $default = null)
 *
 * @see \ErikGall\Samsara\Samsara
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class Samsara extends Facade
{
    /**
     * Create a new SamsaraFake instance for testing.
     */
    public static function fake(): SamsaraFake
    {
        return SamsaraFake::create();
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SamsaraClient::class;
    }
}

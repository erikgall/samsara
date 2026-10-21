<?php

namespace Samsara\Tests\Unit;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the Samsara configuration file.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ConfigTest extends TestCase
{
    #[Test]
    public function it_exposes_the_complete_public_key_set(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertEqualsCanonicalizing(
            ['api_key', 'region', 'timeout', 'attempts', 'retry_writes', 'retry_after_max_seconds', 'per_page', 'webhook_secret'],
            array_keys($config),
        );
    }

    #[Test]
    #[DataProvider('environmentSettings')]
    public function it_reads_each_public_environment_override(
        string $key,
        string $variable,
        string $override,
        mixed $expected,
        mixed $default,
    ): void {
        $this->assertSame($default, $this->configurationWithEnvironment($variable, null)[$key]);
        $this->assertSame($expected, $this->configurationWithEnvironment($variable, $override)[$key]);
    }

    /** @return array<string, array{string, string, string, mixed, mixed}> */
    public static function environmentSettings(): array
    {
        return [
            'token'          => ['api_key', 'SAMSARA_API_KEY', 'environment-token', 'environment-token', null],
            'region'         => ['region', 'SAMSARA_REGION', 'ca', 'ca', 'us'],
            'timeout'        => ['timeout', 'SAMSARA_TIMEOUT', '60', '60', 30],
            'attempts'       => ['attempts', 'SAMSARA_ATTEMPTS', '5', '5', 3],
            'retry writes'   => ['retry_writes', 'SAMSARA_RETRY_WRITES', 'true', true, false],
            'retry ceiling'  => ['retry_after_max_seconds', 'SAMSARA_RETRY_AFTER_MAX_SECONDS', '45', '45', 30],
            'page size'      => ['per_page', 'SAMSARA_PER_PAGE', '50', '50', 100],
            'webhook secret' => ['webhook_secret', 'SAMSARA_WEBHOOK_SECRET', 'environment-secret', 'environment-secret', null],
        ];
    }

    /** @return array<string, mixed> */
    private function configurationWithEnvironment(string $variable, ?string $value): array
    {
        $environment = $_ENV;
        $server = $_SERVER;
        $original = getenv($variable);

        try {
            unset($_ENV[$variable], $_SERVER[$variable]);
            putenv($value === null ? $variable : "{$variable}={$value}");

            if ($value !== null) {
                $_ENV[$variable] = $value;
                $_SERVER[$variable] = $value;
            }

            return require __DIR__.'/../../config/samsara.php';
        } finally {
            $_ENV = $environment;
            $_SERVER = $server;
            putenv($original === false ? $variable : "{$variable}={$original}");
        }
    }
}

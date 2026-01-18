<?php

namespace Samsara\Tests\Unit;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Samsara configuration file.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ConfigTest extends TestCase
{
    #[Test]
    public function config_has_api_key_setting(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertArrayHasKey('api_key', $config);
    }

    #[Test]
    public function config_has_per_page_setting(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertArrayHasKey('per_page', $config);
    }

    #[Test]
    public function config_has_region_setting(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertArrayHasKey('region', $config);
    }

    #[Test]
    public function config_has_retry_setting(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertArrayHasKey('retry', $config);
    }

    #[Test]
    public function config_has_timeout_setting(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertArrayHasKey('timeout', $config);
    }

    #[Test]
    public function default_per_page_is_100(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertSame(100, $config['per_page']);
    }

    #[Test]
    public function default_region_is_us(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        // The default should be 'us' when no env is set
        $this->assertSame('us', $config['region']);
    }

    #[Test]
    public function default_retry_is_3(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertSame(3, $config['retry']);
    }

    #[Test]
    public function default_timeout_is_30_seconds(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertSame(30, $config['timeout']);
    }

    #[Test]
    public function it_loads_configuration_from_config_file(): void
    {
        $config = require __DIR__.'/../../config/samsara.php';

        $this->assertIsArray($config);
    }
}

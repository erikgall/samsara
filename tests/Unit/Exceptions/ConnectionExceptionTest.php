<?php

namespace ErikGall\Samsara\Tests\Unit\Exceptions;

use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Exceptions\SamsaraException;
use ErikGall\Samsara\Exceptions\ConnectionException;

/**
 * Unit tests for the ConnectionException class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ConnectionExceptionTest extends TestCase
{
    #[Test]
    public function it_can_have_custom_message(): void
    {
        $exception = new ConnectionException('Network timeout');

        $this->assertSame('Network timeout', $exception->getMessage());
    }

    #[Test]
    public function it_can_store_context(): void
    {
        $context = ['url' => 'https://api.samsara.com', 'timeout' => 30];
        $exception = new ConnectionException('Connection failed');
        $exception->setContext($context);

        $this->assertSame($context, $exception->getContext());
    }

    #[Test]
    public function it_extends_samsara_exception(): void
    {
        $exception = new ConnectionException('Connection failed');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function it_has_default_code_0(): void
    {
        $exception = new ConnectionException('Connection failed');

        $this->assertSame(0, $exception->getCode());
    }
}

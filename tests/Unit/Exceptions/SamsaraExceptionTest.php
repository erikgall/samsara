<?php

namespace ErikGall\Samsara\Tests\Unit\Exceptions;

use Exception;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Exceptions\SamsaraException;

/**
 * Unit tests for the SamsaraException base class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraExceptionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_code(): void
    {
        $exception = new SamsaraException('Test error', 500);

        $this->assertSame(500, $exception->getCode());
    }

    #[Test]
    public function it_can_be_created_with_context_via_static_method(): void
    {
        $context = ['key' => 'value'];
        $exception = SamsaraException::make('Test error', 500, null, $context);

        $this->assertSame($context, $exception->getContext());
    }

    #[Test]
    public function it_can_be_created_with_message(): void
    {
        $exception = new SamsaraException('Test error message');

        $this->assertSame('Test error message', $exception->getMessage());
    }

    #[Test]
    public function it_can_be_created_with_previous_exception(): void
    {
        $previous = new Exception('Previous error');
        $exception = new SamsaraException('Test error', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function it_can_be_created_with_static_make_method(): void
    {
        $exception = SamsaraException::make('Test error', 500);

        $this->assertInstanceOf(SamsaraException::class, $exception);
        $this->assertSame('Test error', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
    }

    #[Test]
    public function it_can_store_context(): void
    {
        $context = ['endpoint' => '/fleet/drivers', 'method' => 'GET'];
        $exception = new SamsaraException('Test error');
        $exception->setContext($context);

        $this->assertSame($context, $exception->getContext());
    }

    #[Test]
    public function it_extends_base_exception(): void
    {
        $exception = new SamsaraException('Test error');

        $this->assertInstanceOf(Exception::class, $exception);
    }

    #[Test]
    public function it_returns_empty_array_when_no_context(): void
    {
        $exception = new SamsaraException('Test error');

        $this->assertSame([], $exception->getContext());
    }
}

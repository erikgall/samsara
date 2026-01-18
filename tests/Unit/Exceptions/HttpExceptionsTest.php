<?php

namespace Samsara\Tests\Unit\Exceptions;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\ServerException;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\NotFoundException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Exceptions\AuthenticationException;

/**
 * Unit tests for HTTP-specific exception classes.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HttpExceptionsTest extends TestCase
{
    #[Test]
    public function authentication_exception_extends_samsara_exception(): void
    {
        $exception = new AuthenticationException('Unauthorized');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function authentication_exception_has_default_code_401(): void
    {
        $exception = new AuthenticationException('Unauthorized');

        $this->assertSame(401, $exception->getCode());
    }

    #[Test]
    public function authorization_exception_extends_samsara_exception(): void
    {
        $exception = new AuthorizationException('Forbidden');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function authorization_exception_has_default_code_403(): void
    {
        $exception = new AuthorizationException('Forbidden');

        $this->assertSame(403, $exception->getCode());
    }

    #[Test]
    public function not_found_exception_extends_samsara_exception(): void
    {
        $exception = new NotFoundException('Resource not found');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function not_found_exception_has_default_code_404(): void
    {
        $exception = new NotFoundException('Resource not found');

        $this->assertSame(404, $exception->getCode());
    }

    #[Test]
    public function rate_limit_exception_can_store_retry_after(): void
    {
        $exception = new RateLimitException('Too many requests', 60);

        $this->assertSame(60, $exception->getRetryAfter());
    }

    #[Test]
    public function rate_limit_exception_extends_samsara_exception(): void
    {
        $exception = new RateLimitException('Too many requests');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function rate_limit_exception_has_default_code_429(): void
    {
        $exception = new RateLimitException('Too many requests');

        $this->assertSame(429, $exception->getCode());
    }

    #[Test]
    public function rate_limit_exception_returns_null_when_no_retry_after(): void
    {
        $exception = new RateLimitException('Too many requests');

        $this->assertNull($exception->getRetryAfter());
    }

    #[Test]
    public function server_exception_can_have_custom_code(): void
    {
        $exception = new ServerException('Service unavailable', 503);

        $this->assertSame(503, $exception->getCode());
    }

    #[Test]
    public function server_exception_extends_samsara_exception(): void
    {
        $exception = new ServerException('Internal server error');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function server_exception_has_default_code_500(): void
    {
        $exception = new ServerException('Internal server error');

        $this->assertSame(500, $exception->getCode());
    }

    #[Test]
    public function validation_exception_can_store_errors(): void
    {
        $errors = [
            'name'  => ['The name field is required.'],
            'email' => ['The email must be valid.'],
        ];
        $exception = new ValidationException('Validation failed', $errors);

        $this->assertSame($errors, $exception->getErrors());
    }

    #[Test]
    public function validation_exception_extends_samsara_exception(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function validation_exception_has_default_code_422(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertSame(422, $exception->getCode());
    }

    #[Test]
    public function validation_exception_returns_empty_array_when_no_errors(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertSame([], $exception->getErrors());
    }
}

<?php

namespace Samsara\Tests\Unit\Exceptions;

use Exception;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\ServerException;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\NotFoundException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ConnectionException;
use Samsara\Exceptions\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Exceptions\AuthenticationException;
use Samsara\Exceptions\InvalidSignatureException;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for HTTP-specific exception classes.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HttpExceptionsTest extends TestCase
{
    #[Test]
    public function it_exposes_retry_after_seconds(): void
    {
        $exception = new RateLimitException('Too many requests', 60);

        $this->assertSame(60, $exception->getRetryAfter());
    }

    #[Test]
    public function it_exposes_validation_errors(): void
    {
        $errors = [
            'name'  => ['The name field is required.'],
            'email' => ['The email must be valid.'],
        ];
        $exception = new ValidationException('Validation failed', $errors);

        $this->assertSame($errors, $exception->getErrors());
    }

    #[Test]
    public function it_extends_samsara_exception_for_authentication_errors(): void
    {
        $exception = new AuthenticationException('Unauthorized');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function it_extends_samsara_exception_for_authorization_errors(): void
    {
        $exception = new AuthorizationException('Forbidden');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function it_extends_samsara_exception_for_not_found_errors(): void
    {
        $exception = new NotFoundException('Resource not found');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function it_extends_samsara_exception_for_rate_limit_errors(): void
    {
        $exception = new RateLimitException('Too many requests');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function it_extends_samsara_exception_for_server_errors(): void
    {
        $exception = new ServerException('Internal server error');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    #[Test]
    public function it_extends_samsara_exception_for_validation_errors(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertInstanceOf(SamsaraException::class, $exception);
    }

    /**
     * @param  class-string<SamsaraException>  $exceptionClass
     */
    #[Test]
    #[DataProvider('exceptionClasses')]
    public function it_preserves_constructor_status_context_and_cause(string $exceptionClass, int $status): void
    {
        $previous = new Exception('Transport failure');
        $context = ['status' => $status, 'endpoint' => '/fleet/drivers', 'body' => ['error' => 'rejected']];
        $exception = match ($exceptionClass) {
            AuthenticationException::class, AuthorizationException::class,
            ConnectionException::class, NotFoundException::class => new $exceptionClass('API failure', $previous, $context),
            RateLimitException::class                            => new RateLimitException('API failure', 0, $previous, $context),
            ValidationException::class                           => new ValidationException('API failure', ['name' => ['required']], $previous, $context, $status),
            default                                              => new $exceptionClass('API failure', $status, $previous, $context),
        };

        $this->assertSame($exceptionClass, $exception::class);
        $this->assertSame($status, $exception->getCode());
        $this->assertSame($context, $exception->getContext());
        $this->assertSame($previous, $exception->getPrevious());

        if ($exception instanceof RateLimitException) {
            $this->assertSame(0, $exception->getRetryAfter());
        }

        if ($exception instanceof ValidationException) {
            $this->assertSame(['name' => ['required']], $exception->getErrors());
        }
    }

    #[Test]
    public function it_preserves_custom_server_status(): void
    {
        $exception = new ServerException('Service unavailable', 503);

        $this->assertSame(503, $exception->getCode());
    }

    #[Test]
    public function it_preserves_validation_response_status(): void
    {
        $exception = new ValidationException(
            'Validation failed',
            [],
            null,
            ['status' => 400],
            400
        );

        $this->assertSame(400, $exception->getCode());
    }

    #[Test]
    public function it_returns_empty_validation_errors_by_default(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertSame([], $exception->getErrors());
    }

    #[Test]
    public function it_returns_null_when_retry_after_is_unknown(): void
    {
        $exception = new RateLimitException('Too many requests');

        $this->assertNull($exception->getRetryAfter());
    }

    #[Test]
    public function it_uses_401_for_authentication_errors(): void
    {
        $exception = new AuthenticationException('Unauthorized');

        $this->assertSame(401, $exception->getCode());
    }

    #[Test]
    public function it_uses_403_for_authorization_errors(): void
    {
        $exception = new AuthorizationException('Forbidden');

        $this->assertSame(403, $exception->getCode());
    }

    #[Test]
    public function it_uses_404_for_not_found_errors(): void
    {
        $exception = new NotFoundException('Resource not found');

        $this->assertSame(404, $exception->getCode());
    }

    #[Test]
    public function it_uses_422_for_validation_errors(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertSame(422, $exception->getCode());
    }

    #[Test]
    public function it_uses_429_for_rate_limit_errors(): void
    {
        $exception = new RateLimitException('Too many requests');

        $this->assertSame(429, $exception->getCode());
    }

    #[Test]
    public function it_uses_500_for_server_errors(): void
    {
        $exception = new ServerException('Internal server error');

        $this->assertSame(500, $exception->getCode());
    }

    /**
     * @return array<string, array{class-string<SamsaraException>, int}>
     */
    public static function exceptionClasses(): array
    {
        return [
            'base'                  => [SamsaraException::class, 499],
            'authentication'        => [AuthenticationException::class, 401],
            'authorization'         => [AuthorizationException::class, 403],
            'connection'            => [ConnectionException::class, 0],
            'not found'             => [NotFoundException::class, 404],
            'rate limit'            => [RateLimitException::class, 429],
            'server'                => [ServerException::class, 503],
            'validation'            => [ValidationException::class, 422],
            'bad request'           => [ValidationException::class, 400],
            'signature'             => [InvalidSignatureException::class, 401],
            'unsupported operation' => [UnsupportedOperationException::class, 0],
        ];
    }
}

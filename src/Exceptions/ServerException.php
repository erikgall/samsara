<?php

namespace Samsara\Exceptions;

use Throwable;

/**
 * Exception thrown when the API returns a server error (5xx).
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ServerException extends SamsaraException
{
    /**
     * Create a new ServerException instance.
     *
     * @param  string  $message  The exception message
     * @param  int  $code  The HTTP status code (default 500)
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     */
    public function __construct(
        string $message = 'Server error',
        int $code = 500,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}

<?php

namespace Samsara\Exceptions;

use Throwable;

/**
 * Exception thrown when API authentication fails (401 Unauthorized).
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AuthenticationException extends SamsaraException
{
    /**
     * Create a new AuthenticationException instance.
     *
     * @param  string  $message  The exception message
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     */
    public function __construct(
        string $message = 'Authentication failed',
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, 401, $previous, $context);
    }
}

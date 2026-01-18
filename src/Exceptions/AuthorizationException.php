<?php

namespace ErikGall\Samsara\Exceptions;

use Throwable;

/**
 * Exception thrown when API authorization fails (403 Forbidden).
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AuthorizationException extends SamsaraException
{
    /**
     * Create a new AuthorizationException instance.
     *
     * @param  string  $message  The exception message
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     */
    public function __construct(
        string $message = 'Access forbidden',
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, 403, $previous, $context);
    }
}

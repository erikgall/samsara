<?php

namespace ErikGall\Samsara\Exceptions;

use Throwable;

/**
 * Exception thrown when a network connection fails.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ConnectionException extends SamsaraException
{
    /**
     * Create a new ConnectionException instance.
     *
     * @param  string  $message  The exception message
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     */
    public function __construct(
        string $message = 'Connection failed',
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, 0, $previous, $context);
    }
}

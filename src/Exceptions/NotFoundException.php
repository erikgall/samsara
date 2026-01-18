<?php

namespace ErikGall\Samsara\Exceptions;

use Throwable;

/**
 * Exception thrown when a requested resource is not found (404 Not Found).
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class NotFoundException extends SamsaraException
{
    /**
     * Create a new NotFoundException instance.
     *
     * @param  string  $message  The exception message
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     */
    public function __construct(
        string $message = 'Resource not found',
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, 404, $previous, $context);
    }
}

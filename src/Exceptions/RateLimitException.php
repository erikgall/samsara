<?php

namespace ErikGall\Samsara\Exceptions;

use Throwable;

/**
 * Exception thrown when API rate limit is exceeded (429 Too Many Requests).
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RateLimitException extends SamsaraException
{
    /**
     * The number of seconds to wait before retrying.
     */
    protected ?int $retryAfter = null;

    /**
     * Create a new RateLimitException instance.
     *
     * @param  string  $message  The exception message
     * @param  int|null  $retryAfter  Seconds to wait before retrying
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     */
    public function __construct(
        string $message = 'Rate limit exceeded',
        ?int $retryAfter = null,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, 429, $previous, $context);
        $this->retryAfter = $retryAfter;
    }

    /**
     * Get the number of seconds to wait before retrying.
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}

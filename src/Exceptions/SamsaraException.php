<?php

namespace Samsara\Exceptions;

use Exception;
use Throwable;

/**
 * Base exception for all Samsara SDK errors.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraException extends Exception
{
    /**
     * Additional context about the error.
     *
     * @var array<string, mixed>
     */
    protected array $context = [];

    /**
     * Create a new SamsaraException instance.
     *
     * @param  string  $message  The exception message
     * @param  int  $code  The exception code
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get the exception context.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set the exception context.
     *
     * @param  array<string, mixed>  $context  The context to set
     */
    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Create a new exception instance.
     *
     * @param  string  $message  The exception message
     * @param  int  $code  The exception code
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     */
    public static function make(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        array $context = []
    ): static {
        /** @phpstan-ignore new.static */
        return new static($message, $code, $previous, $context);
    }
}

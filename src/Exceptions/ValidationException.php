<?php

namespace Samsara\Exceptions;

use Throwable;

/**
 * Exception thrown when an API request fails validation.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ValidationException extends SamsaraException
{
    /**
     * The validation errors.
     *
     * @var array<string, array<int, string>>
     */
    protected array $errors = [];

    /**
     * Create a new ValidationException instance.
     *
     * @param  string  $message  The exception message
     * @param  array<string, array<int, string>>  $errors  The validation errors
     * @param  Throwable|null  $previous  The previous exception
     * @param  array<string, mixed>  $context  Additional context
     * @param  int  $status  The HTTP response status
     */
    public function __construct(
        string $message = 'Validation failed',
        array $errors = [],
        ?Throwable $previous = null,
        array $context = [],
        int $status = 422
    ) {
        parent::__construct($message, $status, $previous, $context);
        $this->errors = $errors;
    }

    /**
     * Get the validation errors.
     *
     * @return array<string, array<int, string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

<?php

namespace ErikGall\Samsara\Exceptions;

use Throwable;

/**
 * Exception thrown when API request validation fails (422 Unprocessable Entity).
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
     */
    public function __construct(
        string $message = 'Validation failed',
        array $errors = [],
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, 422, $previous, $context);
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

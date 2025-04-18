<?php

declare(strict_types=1);

namespace Martyrer\Throwless\Exception;

use RuntimeException;

/**
 * Exception thrown when attempting to unwrap an invalid Result value.
 */
final class UnwrapException extends RuntimeException
{
    /**
     * Creates a new UnwrapException.
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * Creates an exception for unwrapping an Err value.
     */
    public static function fromUnwrapError(mixed $error): self
    {
        return new self(sprintf(
            'Called unwrap on an Err value: %s',
            get_debug_type($error)
        ));
    }

    /**
     * Creates an exception for unwrapping an Ok value as error.
     */
    public static function fromUnwrapOkAsError(mixed $value): self
    {
        return new self(sprintf(
            'Called unwrap_err on an Ok value: %s',
            get_debug_type($value)
        ));
    }

    /**
     * Creates an exception for expect on an Err value.
     */
    public static function fromExpect(string $msg, mixed $error): self
    {
        return new self(sprintf(
            '%s: %s',
            $msg,
            get_debug_type($error)
        ));
    }

    /**
     * Creates an exception for expect_err on an Ok value.
     */
    public static function fromExpectErr(string $msg, mixed $value): self
    {
        return new self(sprintf(
            '%s: %s',
            $msg,
            get_debug_type($value)
        ));
    }
}

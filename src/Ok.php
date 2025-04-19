<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

use Martyrer\Throwless\Attribute\Pure;
use Martyrer\Throwless\Attribute\ResultType;
use Martyrer\Throwless\Exception\UnwrapException;

/**
 * Represents a successful result containing a value.
 *
 * @template T The type of the contained value
 * @template E The type of the error that would be in an Err
 *
 * @implements Result<T, E>
 */
#[ResultType('Represents a successful Result containing a value')]
final readonly class Ok implements Result
{
    /**
     * @param  T  $value  The success value
     */
    public function __construct(
        private mixed $value
    ) {}

    #[Pure('Always returns true for Ok instances')]
    public function isOk(): bool
    {
        return true;
    }

    #[Pure('Always returns false for Ok instances')]
    public function isErr(): bool
    {
        return false;
    }

    /**
     * @param  T  $default
     * @return T
     */
    #[Pure('For Ok instances, returns the contained value')]
    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    /**
     * @template U
     *
     * @param  callable(T): U  $fn
     * @return Result<U, E>
     */
    #[Pure('Executes the closure with a contained value and returns a new Ok instance or the closure result if it is a Result')]
    public function map(callable $fn): Result
    {
        $result = $fn($this->value);
        if ($result instanceof Result) {
            /** @var Ok<U, E> */
            return $result;
        }

        /** @var Ok<U, E> */
        return new self($result);
    }

    /**
     * @template F
     *
     * @param  callable(E): F  $fn
     * @return Result<T, F>
     */
    #[Pure('For Ok instances, wraps the value in a new Ok')]
    public function mapErr(callable $fn): Result
    {
        /** @var Ok<T, F> */
        return new self($this->value);
    }

    /**
     * @template U
     *
     * @param  callable(T): U  $fn
     * @return Result<U, E>
     */
    #[Pure('Executes the closure with a contained value and returns a new Ok instance or the closure result if it is a Result')]
    public function andThen(callable $fn): Result
    {
        $result = $fn($this->value);
        if ($result instanceof Result) {
            return $result;
        }

        /** @var Ok<U, E> */
        return new self($result);
    }

    /**
     * @param  callable(E): Result<T, E>  $fn
     * @return Result<T, E>
     */
    #[Pure('Returns self for Ok instances')]
    public function orElse(callable $fn): Result
    {
        return $this;
    }

    /**
     * @template U
     *
     * @param  callable(T): U  $okFn
     * @param  callable(E): U  $errFn
     * @return U
     */
    #[Pure('Returns the result of the Ok closure')]
    public function match(callable $okFn, callable $errFn): mixed
    {
        return $okFn($this->value);
    }

    /**
     * @return T
     */
    #[Pure('Returns the contained Ok value')]
    public function unwrap(): mixed
    {
        return $this->value;
    }

    /**
     * @throws UnwrapException
     */
    #[Pure('Throws since this is an Ok value')]
    public function unwrapErr(): never
    {
        throw UnwrapException::fromUnwrapOkAsError($this->value);
    }

    /**
     * @return T
     */
    #[Pure('Returns the contained Ok value without checking')]
    public function unwrapUnchecked(): mixed
    {
        return $this->value;
    }

    /**
     * @return Result<T, E>
     */
    #[Pure('Returns self since this is an Ok value')]
    public function unwrapErrUnchecked(): Result
    {
        return $this;
    }

    /**
     * @param  callable(E): T  $op
     * @return T
     */
    #[Pure('Returns the contained Ok value')]
    public function unwrapOrElse(callable $op): mixed
    {
        return $this->value;
    }

    /**
     * @param  DefaultValueProvider<T>  $provider  The provider for the default value
     * @return T
     */
    #[Pure('Returns the contained Ok value')]
    public function unwrapOrDefault(DefaultValueProvider $provider): mixed
    {
        return $this->value;
    }

    /**
     * @param  callable(T): void  $fn  The function to call with the contained value
     * @return Result<T, E> Returns self for chaining
     */
    #[Pure('Executes provided closure with the contained value. Returns  self for chaining')]
    public function inspect(callable $fn): Result
    {
        try {
            $fn($this->value);
        } finally {
            return $this;
        }
    }

    /**
     * @param  callable(E): void  $fn  The function to call with the contained error
     * @return Result<T, E> Returns self for chaining
     */
    #[Pure('Returns self since this is the Ok variant')]
    public function inspectErr(callable $fn): Result
    {
        return $this;
    }

    /**
     * @param  callable(E): bool  $fn  The closure to call with the contained error
     */
    #[Pure('Returns false since this is the Ok variant. Never calls the closure')]
    public function isErrAnd(callable $fn): bool
    {
        return false;
    }

    /**
     * @param  callable(T): bool  $fn  The closure to call with the contained value
     */
    #[Pure('Executes the closure and returns its result as a boolean')]
    public function isOkAnd(callable $fn): bool
    {
        return (bool)$fn($this->value);
    }

    /**
     * Returns the contained Ok value.
     *
     * @param  string  $msg  The message to use if the value is an Err
     * @return T
     */
    #[Pure('Returns the contained Ok value')]
    public function expect(string $msg): mixed
    {
        return $this->value;
    }

    /**
     * @param  string  $msg  The message to use if the value is an Ok
     *
     * @throws UnwrapException
     */
    #[Pure('Throws with custom message since this is an Ok value')]
    public function expectErr(string $msg): never
    {
        throw UnwrapException::fromExpectErr($msg, $this->value);
    }
}

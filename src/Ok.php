<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

use Martyrer\Throwless\Attribute\Pure;
use Martyrer\Throwless\Attribute\ResultReturn;
use Martyrer\Throwless\Attribute\ResultType;
use Martyrer\Throwless\Attribute\SideEffect;
use Martyrer\Throwless\Exception\UnwrapException;
use Throwable;

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
    #[ResultReturn]
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
    #[ResultReturn]
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
    #[ResultReturn]
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
    #[ResultReturn]
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
    #[SideEffect('Returns the result of the Ok function')]
    public function match(callable $okFn, callable $errFn): mixed
    {
        return $okFn($this->value);
    }

    /**
     * Returns the contained Ok value.
     *
     * @return T
     */
    #[Pure('Returns the contained Ok value')]
    public function unwrap(): mixed
    {
        return $this->value;
    }

    /**
     * Returns the contained Err value, throwing since this is an Ok value.
     *
     * @return never
     *
     * @throws UnwrapException
     */
    #[Pure('Throws since this is an Ok value')]
    public function unwrapErr(): mixed
    {
        throw UnwrapException::fromUnwrapOkAsError($this->value);
    }

    /**
     * Returns the contained Ok value without checking.
     *
     * @return T
     */
    #[Pure('Returns the contained Ok value without checking')]
    public function unwrapUnchecked(): mixed
    {
        return $this->value;
    }

    /**
     * Returns the contained Err value without checking.
     * This will return undefined behavior since this is an Ok value.
     *
     * @return never
     *
     * @throws UnwrapException
     */
    #[Pure('Returns undefined behavior since this is an Ok value')]
    public function unwrapErrUnchecked(): mixed
    {
        throw UnwrapException::fromUnwrapOkAsError($this->value);
    }

    /**
     * Returns the contained Ok value or computes it from a closure.
     *
     * @param  callable(E): T  $op
     * @return T
     */
    public function unwrapOrElse(callable $op): mixed
    {
        return $this->value;
    }

    /**
     * Returns the contained Ok value or the default value for type T.
     *
     * @param  DefaultValueProvider<T>  $provider  The provider for the default value
     * @return T
     */
    public function unwrapOrDefault(DefaultValueProvider $provider): mixed
    {
        return $this->value;
    }

    /**
     * Calls the provided closure with a reference to the contained value (if Ok).
     *
     * @param  callable(T): void  $fn  The function to call with the contained value
     * @return Result<T, E> Returns self for chaining
     */
    #[ResultReturn]
    public function inspect(callable $fn): Result
    {
        try {
            $fn($this->value);
        } finally {
            return $this;
        }
    }

    /**
     * Calls the provided closure with a reference to the contained error (if Err).
     *
     * @param  callable(E): void  $fn  The function to call with the contained error
     * @return Result<T, E> Returns self for chaining
     */
    #[ResultReturn]
    public function inspectErr(callable $fn): Result
    {
        return $this;
    }

    /**
     * Checks if the result is Err and the error value matches a predicate.
     *
     * @param  callable(E): bool  $fn  The predicate to check against the error value
     */
    public function isErrAnd(callable $fn): bool
    {
        return false;
    }

    /**
     * Checks if the result is Ok and the contained value matches a predicate.
     *
     * @param  callable(T): bool  $fn  The predicate to check against the contained value
     */
    public function isOkAnd(callable $fn): bool
    {
        try {
            return $fn($this->value);
        } catch (Throwable) {
            return false;
        }
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
     * Returns the contained Err value or throws with the provided message.
     *
     * @param  string  $msg  The message to use if the value is an Ok
     * @return never
     *
     * @throws UnwrapException
     */
    #[Pure('Throws with custom message since this is an Ok value')]
    public function expectErr(string $msg): mixed
    {
        throw UnwrapException::fromExpectErr($msg, $this->value);
    }
}

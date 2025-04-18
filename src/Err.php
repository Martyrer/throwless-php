<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

use Martyrer\Throwless\Attribute\Pure;
use Martyrer\Throwless\Attribute\ResultReturn;
use Martyrer\Throwless\Attribute\ResultType;
use Martyrer\Throwless\Exception\UnwrapException;
use Throwable;

/**
 * Represents a failed result containing an error.
 *
 * @template T The type that would be in an Ok
 * @template E The type of the error
 *
 * @implements Result<T, E>
 */
#[ResultType('Represents a failed Result containing an error')]
final readonly class Err implements Result
{
    /**
     * @param  E  $error  The error value
     */
    public function __construct(
        private mixed $error
    ) {}

    #[Pure('Always returns false for Err instances')]
    public function isOk(): bool
    {
        return false;
    }

    #[Pure('Always returns true for Err instances')]
    public function isErr(): bool
    {
        return true;
    }

    /**
     * @param  T  $default
     * @return T
     */
    #[Pure('Returns the default value for Err instances')]
    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    /**
     * @template U
     *
     * @param  callable(T): U  $fn
     * @return Result<U, E>
     */
    #[Pure('Returns self for Err instances')]
    #[ResultReturn]
    public function map(callable $fn): Result
    {
        return $this;
    }

    /**
     * @template F
     *
     * @param  callable(E): F  $fn
     * @return Result<T, F>
     */
    #[Pure('Applies the function to the contained error')]
    #[ResultReturn]
    public function mapErr(callable $fn): Result
    {
        $result = $fn($this->error);
        if ($result instanceof Result) {
            /** @var Err<T, F> */
            return $result;
        }

        /** @var Err<T, F> */
        return new self($result);
    }

    /**
     * @template U
     *
     * @param  callable(T): Result<U, E>  $fn
     * @return Result<U, E>
     */
    #[Pure('Returns self for Err instances')]
    #[ResultReturn]
    public function andThen(callable $fn): Result
    {
        return $this;
    }

    /**
     * @template F
     *
     * @param  callable(E): F  $fn
     * @return Result<T, F>
     */
    #[Pure('Chains the Result-returning function on error')]
    #[ResultReturn]
    public function orElse(callable $fn): Result
    {
        $result = $fn($this->error);
        if ($result instanceof Result) {
            return $result;
        }

        /** @var Err<T, F> */
        return new self($result);
    }

    /**
     * @template U
     *
     * @param  callable(T): U  $okFn
     * @param  callable(E): U  $errFn
     * @return U
     */
    #[Pure('Applies the error function to the contained error')]
    public function match(callable $okFn, callable $errFn): mixed
    {
        return $errFn($this->error);
    }

    /**
     * Returns the contained Ok value, throwing since this is an Err value.
     *
     * @return T
     *
     * @throws UnwrapException
     */
    #[Pure('Throws since this is an Err value')]
    public function unwrap(): mixed
    {
        throw UnwrapException::fromUnwrapError($this->error);
    }

    /**
     * Returns the contained Err value.
     *
     * @return E
     */
    #[Pure('Returns the contained Err value')]
    public function unwrapErr(): mixed
    {
        return $this->error;
    }

    /**
     * Returns the contained Ok value without checking.
     * This will return undefined behavior since this is an Err value.
     *
     * @return E
     */
    #[Pure('Returns undefined behavior since this is an Err value')]
    public function unwrapUnchecked(): mixed
    {
        return $this->error; // this is technically undefined behavior
    }

    /**
     * Returns the contained Err value without checking.
     *
     * @return E
     */
    #[Pure('Returns the contained Err value without checking')]
    public function unwrapErrUnchecked(): mixed
    {
        return $this->error;
    }

    /**
     * Returns the contained Ok value or computes it from a closure.
     *
     * @param  callable(E): T  $op
     * @return Result<T, E>
     */
    #[Pure('Computes the value from the error using the provided closure')]
    #[ResultReturn]
    public function unwrapOrElse(callable $op): Result
    {
        /** @var Ok<T, E> */
        return new Ok($op($this->error));
    }

    /**
     * Returns the contained Ok value or the default value for type T.
     *
     * @param  DefaultValueProvider<T>  $provider  The provider for the default value
     * @return T
     */
    #[Pure('Returns the default value for type T')]
    public function unwrapOrDefault(DefaultValueProvider $provider): mixed
    {
        return $provider->getDefaultValue(get_debug_type($this->error));
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
        return $this;
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
        $fn($this->error);

        return $this;
    }

    /**
     * Checks if the result is Err and the error value matches a predicate.
     *
     * @param  callable(E): bool  $fn  The predicate to check against the error value
     */
    #[Pure('Checks if the error value matches the predicate')]
    public function isErrAnd(callable $fn): bool
    {
        try {
            return $fn($this->error);
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Checks if the result is Ok and the contained value matches a predicate.
     *
     * @param  callable(T): bool  $fn  The predicate to check against the contained value
     */
    #[Pure('Always returns false for Err instances')]
    public function isOkAnd(callable $fn): bool
    {
        return false;
    }

    /**
     * Returns the contained Ok value or throws with the provided message.
     *
     * @param  string  $msg  The message to use if the value is an Err
     * @return T
     *
     * @throws UnwrapException if the value is an Err
     */
    #[Pure('Throws with custom message since this is an Err value')]
    public function expect(string $msg): mixed
    {
        throw UnwrapException::fromExpect($msg, $this->error);
    }

    /**
     * Returns the contained Err value or throws with the provided message.
     *
     * @param  string  $msg  The message to use if the value is an Ok
     * @return E
     */
    #[Pure('Returns the contained Err value')]
    public function expectErr(string $msg): mixed
    {
        return $this->error;
    }
}

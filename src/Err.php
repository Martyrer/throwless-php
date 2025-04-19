<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

use Martyrer\Throwless\Attribute\Pure;
use Martyrer\Throwless\Attribute\ResultType;
use Martyrer\Throwless\Exception\UnwrapException;

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

    #[Pure('For Err instances, always returns the default')]
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
    #[Pure('Executes the closure with a contained error and returns a new Err instance or the closure result if it is a Result')]
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
    #[Pure('Executes the closure with a contained error and returns a new Err instance or the closure result if it is a Result')]
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
    #[Pure('Returns the result of the Err closure')]
    public function match(callable $okFn, callable $errFn): mixed
    {
        return $errFn($this->error);
    }

    /**
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
     * @return E
     */
    #[Pure('Returns self since this is an Err value')]
    public function unwrapUnchecked(): mixed
    {
        return $this->error; // this is technically undefined behavior
    }

    /**
     * @return E
     */
    #[Pure('Returns the contained Err value without checking')]
    public function unwrapErrUnchecked(): mixed
    {
        return $this->error;
    }

    /**
     * @param  callable(E): T  $op
     * @return Result<T, E>
     */
    #[Pure('Computes the value from the error using the provided closure. Returns a new Ok instance')]
    public function unwrapOrElse(callable $op): Result
    {
        /** @var Ok<T, E> */
        return new Ok($op($this->error));
    }

    /**
     * @param  DefaultValueProvider<T>  $provider  The provider for the default value
     * @return T
     */
    #[Pure('Returns the default value for type T')]
    public function unwrapOrDefault(DefaultValueProvider $provider): mixed
    {
        return $provider->getDefaultValue(get_debug_type($this->error));
    }

    /**
     * @param  callable(T): void  $fn  The function to call with the contained value
     * @return Result<T, E> Returns self for chaining
     */
    #[Pure('Returns self since this is an Err value')]
    public function inspect(callable $fn): Result
    {
        return $this;
    }

    /**
     * @param  callable(E): void  $fn  The function to call with the contained error
     * @return Result<T, E> Returns self for chaining
     */
    #[Pure('Executes provided closure with the contained error. Returns self for chaining')]
    public function inspectErr(callable $fn): Result
    {
        try {
            $fn($this->error);
        } finally {
            return $this;
        }
    }

    /**
     * @param  callable(E): bool  $fn  The closure to call with the contained error
     */
    #[Pure('Executes the closure and returns its result as a boolean')]
    public function isErrAnd(callable $fn): bool
    {
        return (bool)$fn($this->error);
    }

    /**
     * @param  callable(T): bool  $fn  The closure to call with the contained value
     */
    #[Pure('Returns false since this is the Err variant. Never calls the closure')]
    public function isOkAnd(callable $fn): bool
    {
        return false;
    }

    /**
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
     * @param  string  $msg  The message to use if the value is an Ok
     * @return E
     */
    #[Pure('Returns the contained Err value')]
    public function expectErr(string $msg): mixed
    {
        return $this->error;
    }
}

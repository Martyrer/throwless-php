<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

use Martyrer\Throwless\Attribute\ResultType;
use Martyrer\Throwless\Exception\UnwrapException;

/**
 * Result type interface representing either success (Ok) or failure (Err).
 *
 * @template T The success value type
 * @template E The error value type
 */
#[ResultType('Base interface for Result types representing either success (Ok) or failure (Err)')]
interface Result
{
    /**
     * Returns true if the result is Ok.
     */
    public function isOk(): bool;

    /**
     * Returns true if the result is Err.
     */
    public function isErr(): bool;

    /**
     * Unwraps a result, yielding the content of an Ok.
     * Returns the default value if the result is Err.
     *
     * @param  T  $default  The default value to return if this is an Err
     * @return T The contained Ok value or the default value
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * Maps a Result<T, E> to Result<U, E> by applying a function to a contained Ok value,
     * leaving an Err value untouched.
     *
     * @template U
     *
     * @param  callable(T): U  $fn  The function to apply to the Ok value
     * @return Result<U, E> A new Result with the mapped value
     */
    public function map(callable $fn): self;

    /**
     * Maps a Result<T, E> to Result<T, F> by applying a function to a contained Err value,
     * leaving an Ok value untouched.
     *
     * @template F
     *
     * @param  callable(E): F  $fn  The function to apply to the Err value
     * @return Result<T, F> A new Result with the mapped error
     */
    public function mapErr(callable $fn): self;

    /**
     * Returns Result from applying fn to the contained Ok value, or returns the Err value.
     *
     * @template U
     *
     * @param  callable(T): Result<U, E>  $fn  The function to apply to the Ok value
     * @return Result<U, E> The result of applying fn or the original Err
     */
    public function andThen(callable $fn): self;

    /**
     * Calls fn if the result is Err, otherwise returns the Ok value.
     *
     * @param  callable(E): Result<T, E>  $fn  The function to apply to the Err value
     * @return Result<T, E> The result of applying fn or the original Ok
     */
    public function orElse(callable $fn): self;

    /**
     * Pattern matches on the Result, applying okFn if Ok, errFn if Err.
     *
     * @template U
     *
     * @param  callable(T): U  $okFn  Function to apply to Ok value
     * @param  callable(E): U  $errFn  Function to apply to Err value
     * @return U The result of applying the appropriate function
     */
    public function match(callable $okFn, callable $errFn): mixed;

    /**
     * Returns the contained Ok value, throwing if the value is an Err.
     *
     * @return T
     *
     * @throws UnwrapException if the value is an Err
     */
    public function unwrap(): mixed;

    /**
     * Returns the contained Err value, throwing if the value is Ok.
     *
     * @return E
     *
     * @throws UnwrapException if the value is Ok
     */
    public function unwrapErr(): mixed;

    /**
     * Returns the contained Ok value without checking if it is Ok.
     * This is a dangerous operation that should only be used when you are absolutely certain that the value is Ok.
     *
     * @return T
     */
    public function unwrapUnchecked(): mixed;

    /**
     * Returns the contained Err value without checking if it is Err.
     * This is a dangerous operation that should only be used when you are absolutely certain that the value is Err.
     *
     * @return Result<T, E>|E
     */
    public function unwrapErrUnchecked(): mixed;

    /**
     * Returns the contained Ok value or computes it from a closure.
     *
     * @param  callable(E): T  $op
     * @return T
     */
    public function unwrapOrElse(callable $op): mixed;

    /**
     * Returns the contained Ok value or the default value for type T.
     *
     * @param  DefaultValueProvider<T>  $provider  The provider for the default value
     * @return T
     */
    public function unwrapOrDefault(DefaultValueProvider $provider): mixed;

    /**
     * Calls the provided closure with a reference to the contained value (if Ok).
     *
     * @param  callable(T): void  $fn  The function to call with the contained value
     * @return Result<T, E> Returns self for chaining
     */
    public function inspect(callable $fn): self;

    /**
     * Calls the provided closure with a reference to the contained error (if Err).
     *
     * @param  callable(E): void  $fn  The function to call with the contained error
     * @return Result<T, E> Returns self for chaining
     */
    public function inspectErr(callable $fn): self;

    /**
     * Executes the closure and returns its result as a boolean if the result is Err.
     *
     * @param  callable(E): bool  $fn  The closure to call with the contained error
     */
    public function isErrAnd(callable $fn): bool;

    /**
     * Executes the closure and returns its result as a boolean if the result is Ok.
     *
     * @param  callable(T): bool  $fn  The closure to call with the contained value
     */
    public function isOkAnd(callable $fn): bool;

    /**
     * Returns the contained Ok value or throws with the provided message.
     *
     * @param  string  $msg  The message to use if the value is an Err
     * @return T
     *
     * @throws UnwrapException if the value is an Err
     */
    public function expect(string $msg): mixed;

    /**
     * Returns the contained Err value or throws with the provided message.
     *
     * @param  string  $msg  The message to use if the value is an Ok
     * @return E
     *
     * @throws UnwrapException if the value is an Ok
     */
    public function expectErr(string $msg): mixed;
}

<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

use Martyrer\Throwless\Attribute\Pure;
use Martyrer\Throwless\Attribute\ResultReturn;
use Martyrer\Throwless\Attribute\ResultType;
use Martyrer\Throwless\Attribute\SideEffect;
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
    #[Pure('Checks if the result is a success (Ok) instance')]
    public function isOk(): bool;

    /**
     * Returns true if the result is Err.
     */
    #[Pure('Checks if the result is an error (Err) instance')]
    public function isErr(): bool;

    /**
     * Unwraps a result, yielding the content of an Ok.
     * Returns the default value if the result is Err.
     *
     * @param  T  $default  The default value to return if this is an Err
     * @return T The contained Ok value or the default value
     */
    #[SideEffect('Default value could be computed or have side effects')]
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
    #[SideEffect('Callback could modify state')]
    #[ResultReturn]
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
    #[SideEffect('Callback could modify state')]
    #[ResultReturn]
    public function mapErr(callable $fn): self;

    /**
     * Returns Result from applying fn to the contained Ok value, or returns the Err value.
     *
     * @template U
     *
     * @param  callable(T): Result<U, E>  $fn  The function to apply to the Ok value
     * @return Result<U, E> The result of applying fn or the original Err
     */
    #[SideEffect('Callback returns Result and could have side effects')]
    #[ResultReturn]
    public function andThen(callable $fn): self;

    /**
     * Calls fn if the result is Err, otherwise returns the Ok value.
     *
     * @param  callable(E): Result<T, E>  $fn  The function to apply to the Err value
     * @return Result<T, E> The result of applying fn or the original Ok
     */
    #[SideEffect('Callback returns Result and could have side effects')]
    #[ResultReturn]
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
    #[SideEffect('Both callbacks could have side effects')]
    public function match(callable $okFn, callable $errFn): mixed;

    /**
     * Returns the contained Ok value, throwing if the value is an Err.
     *
     * @return T
     *
     * @throws UnwrapException if the value is an Err
     */
    #[Pure('Returns the contained Ok value or throws if Err')]
    public function unwrap(): mixed;

    /**
     * Returns the contained Err value, throwing if the value is Ok.
     *
     * @return E
     *
     * @throws UnwrapException if the value is Ok
     */
    #[Pure('Returns the contained Err value or throws if Ok')]
    public function unwrapErr(): mixed;

    /**
     * Returns the contained Ok value without checking if it is Ok.
     * This is a dangerous operation that should only be used when you are absolutely certain that the value is Ok.
     *
     * @return T
     */
    #[Pure('Returns the contained Ok value without checking')]
    public function unwrapUnchecked(): mixed;

    /**
     * Returns the contained Err value without checking if it is Err.
     * This is a dangerous operation that should only be used when you are absolutely certain that the value is Err.
     *
     * @return E
     */
    #[Pure('Returns the contained Err value without checking')]
    public function unwrapErrUnchecked(): mixed;

    /**
     * Returns the contained Ok value or computes it from a closure.
     *
     * @param  callable(E): T  $op
     * @return T
     */
    #[SideEffect('Callback could have side effects')]
    public function unwrapOrElse(callable $op): mixed;

    /**
     * Returns the contained Ok value or the default value for type T.
     *
     * @param  DefaultValueProvider<T>  $defaultValueProvider  The provider for the default value
     * @return T
     */
    #[SideEffect('Provider could have side effects')]
    public function unwrapOrDefault(DefaultValueProvider $defaultValueProvider): mixed;

    /**
     * Calls the provided closure with a reference to the contained value (if Ok).
     *
     * @param  callable(T): void  $fn  The function to call with the contained value
     * @return Result<T, E> Returns self for chaining
     */
    #[SideEffect('Designed for side effects through callback')]
    #[ResultReturn]
    public function inspect(callable $fn): self;

    /**
     * Calls the provided closure with a reference to the contained error (if Err).
     *
     * @param  callable(E): void  $fn  The function to call with the contained error
     * @return Result<T, E> Returns self for chaining
     */
    #[SideEffect('Designed for side effects through callback')]
    #[ResultReturn]
    public function inspectErr(callable $fn): self;

    /**
     * Checks if the result is Err and the error value matches a predicate.
     *
     * @param  callable(E): bool  $fn  The predicate to check against the error value
     */
    #[SideEffect('Predicate could have side effects')]
    public function isErrAnd(callable $fn): bool;

    /**
     * Checks if the result is Ok and the contained value matches a predicate.
     *
     * @param  callable(T): bool  $fn  The predicate to check against the contained value
     */
    #[SideEffect('Predicate could have side effects')]
    public function isOkAnd(callable $fn): bool;

    /**
     * Returns the contained Ok value or throws with the provided message.
     *
     * @param  string  $msg  The message to use if the value is an Err
     * @return T
     *
     * @throws UnwrapException if the value is an Err
     */
    #[Pure('Returns the contained Ok value or throws with custom message')]
    public function expect(string $msg): mixed;

    /**
     * Returns the contained Err value or throws with the provided message.
     *
     * @param  string  $msg  The message to use if the value is an Ok
     * @return E
     *
     * @throws UnwrapException if the value is an Ok
     */
    #[Pure('Returns the contained Err value or throws with custom message')]
    public function expectErr(string $msg): mixed;
}

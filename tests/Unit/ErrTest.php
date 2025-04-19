<?php

declare(strict_types=1);

use Martyrer\Throwless\BasicDefaultValueProvider;
use Martyrer\Throwless\Err;
use Martyrer\Throwless\Exception\UnwrapException;
use Martyrer\Throwless\Exception\ValidationException;
use Martyrer\Throwless\Ok;

test('Err::unwrap throws UnwrapException', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect(fn (): mixed => $err->unwrap())->toThrow(UnwrapException::class);
});

test('Err::unwrapOr returns default value', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->unwrapOr(42))->toBe(42);
});

test('Err::unwrapOrElse returns callback result', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);
    $result = $err->unwrapOrElse(static fn (): int => 42);

    expect($result)->toBeInstanceOf(Ok::class)->and($result->unwrap())->toBe(42);
});

test('Err::isOk returns false', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->isOk())->toBeFalse();
});

test('Err::isErr returns true', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->isErr())->toBeTrue();
});

test('Err::match calls err branch', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    $result = $err->match(
        fn ($value): int|float => $value * 2,
        fn ($error): int => 42
    );

    expect($result)->toBe(42);
});

test('Err::andThen skips operation', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    $result = $err->andThen(fn ($value): Ok => new Ok($value * 2));

    expect($result)
        ->toBeInstanceOf(Err::class)
        ->and($result->unwrapErr())->toBe($error);
});

test('Err::orElse handles error', function (): void {
    $error = new ValidationException('Original error');
    $err = new Err($error);

    $result = $err->orElse(fn ($error): Ok => new Ok(42));

    expect($result)
        ->toBeInstanceOf(Ok::class)
        ->and($result->unwrap())->toBe(42);
});

test('Err::map skips value transformation', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    $result = $err->map(fn ($value): int|float => $value * 2);

    expect($result)
        ->toBeInstanceOf(Err::class)
        ->and($result->unwrapErr())->toBe($error);
});

test('Err::mapErr transforms error', function (): void {
    $error = new ValidationException('Original error');
    $err = new Err($error);

    $result = $err
        ->mapErr(
            fn ($error): ValidationException => new ValidationException('Transformed: '.$error->getMessage())
        );

    expect($result)
        ->toBeInstanceOf(Err::class)
        ->and($result->unwrapErr()->getMessage())->toBe('Transformed: Original error');
});

test('Err::unwrapUnchecked returns error value', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->unwrapUnchecked())->toBe($error);
});

test('Err::unwrapErrUnchecked returns error value', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->unwrapErrUnchecked())->toBe($error);
});

test('Err::unwrapOrDefault returns default value from provider', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);
    $provider = new BasicDefaultValueProvider();

    expect($err->unwrapOrDefault($provider))->toBeNull();
});

test('Err::inspect skips callback and returns self', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);
    $called = false;

    $result = $err->inspect(function () use (&$called): void {
        $called = true;
    });

    expect($called)->toBeFalse()
        ->and($result)->toBe($err);
});

test('Err::inspectErr calls callback and returns self', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);
    $called = false;

    $result = $err->inspectErr(function ($e) use (&$called, $error): void {
        $called = true;
        expect($e)->toBe($error);
    });

    expect($called)->toBeTrue()
        ->and($result)->toBe($err);
});

test('Err::isErrAnd returns true when predicate matches', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->isErrAnd(fn ($e): true => $e instanceof ValidationException))->toBeTrue();
});

test('Err::isErrAnd returns false when predicate does not match', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->isErrAnd(fn ($e): false => $e instanceof UnwrapException))->toBeFalse();
});

test('Err::isErrAnd throws when closure throws', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect(static fn (): mixed => $err->isErrAnd(function (): void {
        throw new RuntimeException('Predicate error');
    }))->toThrow(RuntimeException::class);
});

test('Err::isOkAnd always returns false', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->isOkAnd(fn ($v): true => true))->toBeFalse();
});

test('Err::expect throws UnwrapException with custom message', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);
    $message = 'Custom error message';

    expect(static fn (): mixed => $err->expect($message))
        ->toThrow(UnwrapException::class, $message);
});

test('Err::expectErr returns error value', function (): void {
    $error = new ValidationException('Test error');
    $err = new Err($error);

    expect($err->expectErr('Should not throw'))->toBe($error);
});

test('Err::mapErr with result returning function', function (): void {
    $err = new Err('error');
    $result = $err->mapErr(fn ($e): Ok => new Ok('mapped'));
    expect($result)->toBeInstanceOf(Ok::class);
    expect($result->unwrap())->toBe('mapped');
});

test('Err::orElse with result returning function', function (): void {
    $err = new Err('error');
    $result = $err->orElse(fn ($e): Ok => new Ok('recovered'));
    expect($result)->toBeInstanceOf(Ok::class);
    expect($result->unwrap())->toBe('recovered');
});

test('Err::orElse with non-Result returning function', function (): void {
    $err = new Err('error');
    $result = $err->orElse(fn ($e): string => 'handled error');
    expect($result)->toBeInstanceOf(Err::class);
    expect($result->unwrapErr())->toBe('handled error');
});

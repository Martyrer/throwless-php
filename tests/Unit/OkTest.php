<?php

declare(strict_types=1);

use Martyrer\Throwless\BasicDefaultValueProvider;
use Martyrer\Throwless\Exception\UnwrapException;
use Martyrer\Throwless\Ok;

test('Ok::unwrap returns the value', function (): void {
    $ok = new Ok(42);
    expect($ok->unwrap())->toBe(42);
});

test('Ok::unwrapOr returns the value', function (): void {
    $ok = new Ok(42);
    expect($ok->unwrapOr(0))->toBe(42);
});

test('Ok::unwrapOrElse returns the value', function (): void {
    $ok = new Ok(42);
    expect($ok->unwrapOrElse(fn (): int => 0))->toBe(42);
});

test('Ok::isOk returns true', function (): void {
    $ok = new Ok(42);
    expect($ok->isOk())->toBeTrue();
});

test('Ok::isErr returns false', function (): void {
    $ok = new Ok(42);
    expect($ok->isErr())->toBeFalse();
});

test('Ok::match calls ok branch', function (): void {
    $ok = new Ok(42);
    $result = $ok->match(
        fn ($value): int => $value * 2,
        fn ($error): int => 0
    );
    expect($result)->toBe(84);
});

test('Ok::andThen chains operations', function (): void {
    $ok = new Ok(21);
    $result = $ok->andThen(fn ($value): Ok => new Ok($value * 2));

    expect($result)
        ->toBeInstanceOf(Ok::class)
        ->and($result->unwrap())->toBe(42);
});

test('Ok::andThen with non-Result returning function', function (): void {
    $ok = new Ok(21);
    $result = $ok->andThen(fn ($value): int => $value * 2);

    expect($result)
        ->toBeInstanceOf(Ok::class)
        ->and($result->unwrap())->toBe(42);
});

test('Ok::orElse skips error handling', function (): void {
    $ok = new Ok(42);
    $result = $ok->orElse(fn ($error): Ok => new Ok(0));

    expect($result)
        ->toBeInstanceOf(Ok::class)
        ->and($result->unwrap())->toBe(42);
});

test('Ok::map transforms value', function (): void {
    $ok = new Ok(21);
    $result = $ok->map(fn ($value): int => $value * 2);

    expect($result)
        ->toBeInstanceOf(Ok::class)
        ->and($result->unwrap())->toBe(42);
});

test('Ok::map with Result returning function', function (): void {
    $ok = new Ok(21);
    $result = $ok->map(fn ($value): Ok => new Ok($value * 2));

    expect($result)
        ->toBeInstanceOf(Ok::class)
        ->and($result->unwrap())->toBe(42);
});

test('Ok::mapErr does not transform value', function (): void {
    $ok = new Ok(42);
    $result = $ok->mapErr(fn ($error): Exception => new Exception('Should not transform'));

    expect($result)
        ->toBeInstanceOf(Ok::class)
        ->and($result->unwrap())->toBe(42);
});

test('Ok::unwrapErr throws UnwrapException', function (): void {
    $ok = new Ok(42);
    expect(fn (): mixed => $ok->unwrapErr())->toThrow(UnwrapException::class);
});

test('Ok::unwrapUnchecked returns the value', function (): void {
    $ok = new Ok(42);
    expect($ok->unwrapUnchecked())->toBe(42);
});

test('Ok::unwrapErrUnchecked throws UnwrapException', function (): void {
    $ok = new Ok(42);
    expect(fn (): mixed => $ok->unwrapErrUnchecked())->toThrow(UnwrapException::class);
});

test('Ok::unwrapOrDefault returns the value', function (): void {
    $ok = new Ok(42);
    $provider = new BasicDefaultValueProvider();
    expect($ok->unwrapOrDefault($provider))->toBe(42);
});

test('Ok::inspect calls callback and returns self', function (): void {
    $ok = new Ok(42);
    $called = false;
    $inspected = null;

    $result = $ok->inspect(function ($value) use (&$called, &$inspected): void {
        $called = true;
        $inspected = $value;
    });

    expect($called)->toBeTrue()
        ->and($inspected)->toBe(42)
        ->and($result)->toBe($ok);
});

test('Ok::inspectErr skips callback and returns self', function (): void {
    $ok = new Ok(42);
    $called = false;

    $result = $ok->inspectErr(function () use (&$called): void {
        $called = true;
    });

    expect($called)->toBeFalse()
        ->and($result)->toBe($ok);
});

test('Ok::isErrAnd always returns false', function (): void {
    $ok = new Ok(42);
    expect($ok->isErrAnd(fn ($e): true => true))->toBeFalse();
});

test('Ok::isOkAnd returns true when predicate matches', function (): void {
    $ok = new Ok(42);
    expect($ok->isOkAnd(fn ($v): bool => $v === 42))->toBeTrue();
});

test('Ok::isOkAnd returns false when predicate does not match', function (): void {
    $ok = new Ok(42);
    expect($ok->isOkAnd(fn ($v): bool => $v === 0))->toBeFalse();
});

test('Ok::isOkAnd returns false when predicate throws', function (): void {
    $ok = new Ok(42);
    expect($ok->isOkAnd(function (): void {
        throw new RuntimeException('Predicate error');
    }))->toBeFalse();
});

test('Ok::expect returns the value', function (): void {
    $ok = new Ok(42);
    expect($ok->expect('Should not throw'))->toBe(42);
});

test('Ok::expectErr throws UnwrapException with custom message', function (): void {
    $ok = new Ok(42);
    $message = 'Custom error message';

    expect(static fn (): mixed => $ok->expectErr($message))
        ->toThrow(UnwrapException::class, $message);
});

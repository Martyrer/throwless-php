<?php

declare(strict_types=1);

use Martyrer\Throwless\Exception\UnwrapException;

test('UnwrapException can be constructed with message', function (): void {
    $message = 'Test error message';
    $exception = new UnwrapException($message);

    expect($exception->getMessage())->toBe($message);
});

test('UnwrapException::fromUnwrapError creates exception for Err value', function (): void {
    $error = new stdClass();
    $exception = UnwrapException::fromUnwrapError($error);

    expect($exception->getMessage())->toBe('Called unwrap on an Err value: stdClass');
});

test('UnwrapException::fromUnwrapOkAsError creates exception for Ok value', function (): void {
    $value = 'test value';
    $exception = UnwrapException::fromUnwrapOkAsError($value);

    expect($exception->getMessage())->toBe('Called unwrap_err on an Ok value: string');
});

test('UnwrapException::fromExpect creates exception with custom message', function (): void {
    $msg = 'Expected value';
    $error = 42;
    $exception = UnwrapException::fromExpect($msg, $error);

    expect($exception->getMessage())->toBe('Expected value: int');
});

test('UnwrapException::fromExpectErr creates exception with custom message', function (): void {
    $msg = 'Expected error';
    $value = ['test'];
    $exception = UnwrapException::fromExpectErr($msg, $value);

    expect($exception->getMessage())->toBe('Expected error: array');
});

test('UnwrapException handles different value types', function (): void {
    // Test with null
    expect(UnwrapException::fromUnwrapError(null)->getMessage())
        ->toBe('Called unwrap on an Err value: null');

    // Test with array
    expect(UnwrapException::fromUnwrapOkAsError([])->getMessage())
        ->toBe('Called unwrap_err on an Ok value: array');

    // Test with callable
    $callable = fn (): null => null;
    expect(UnwrapException::fromExpect('Test', $callable)->getMessage())
        ->toBe('Test: Closure');

    // Test with resource (if available)
    $resource = fopen('php://memory', 'r');
    expect(UnwrapException::fromExpectErr('Test', $resource)->getMessage())
        ->toMatch('/^Test: resource/');
    fclose($resource);
});

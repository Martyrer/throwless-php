<?php

declare(strict_types=1);

use Martyrer\Throwless\Attribute\ResultReturn;
use Martyrer\Throwless\Result;

test('ResultReturn can be constructed with default value', function (): void {
    $attribute = new ResultReturn();
    expect($attribute->expectedType)->toBe(Result::class);
});

test('ResultReturn can be constructed with custom type', function (): void {
    $customType = 'CustomResult';
    $attribute = new ResultReturn($customType);
    expect($attribute->expectedType)->toBe($customType);
});

test('ResultReturn is method-only attribute', function (): void {
    $reflection = new ReflectionClass(ResultReturn::class);
    $attributes = $reflection->getAttributes();

    expect($attributes)->toHaveCount(1)
        ->and($attributes[0]->getName())->toBe(Attribute::class)
        ->and($attributes[0]->getArguments())->toBe([Attribute::TARGET_METHOD]);
});

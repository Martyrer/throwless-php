<?php

declare(strict_types=1);

use Martyrer\Throwless\Attribute\ResultType;

test('ResultType can be constructed with description', function (): void {
    $description = 'This is a Result type';
    $attribute = new ResultType($description);
    expect($attribute->description)->toBe($description);
});

test('ResultType is class-only attribute', function (): void {
    $reflection = new ReflectionClass(ResultType::class);
    $attributes = $reflection->getAttributes();

    expect($attributes)->toHaveCount(1)
        ->and($attributes[0]->getName())->toBe(Attribute::class)
        ->and($attributes[0]->getArguments())->toBe([Attribute::TARGET_CLASS]);
});

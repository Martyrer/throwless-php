<?php

declare(strict_types=1);

use Martyrer\Throwless\Attribute\Pure;

test('Pure can be constructed with default empty description', function (): void {
    $attribute = new Pure();
    expect($attribute->description)->toBe('');
});

test('Pure can be constructed with custom description', function (): void {
    $description = 'This method is pure';
    $attribute = new Pure($description);
    expect($attribute->description)->toBe($description);
});

test('Pure is method-only attribute', function (): void {
    $reflection = new ReflectionClass(Pure::class);
    $attributes = $reflection->getAttributes();

    expect($attributes)->toHaveCount(1)
        ->and($attributes[0]->getName())->toBe(Attribute::class)
        ->and($attributes[0]->getArguments())->toBe([Attribute::TARGET_METHOD]);
});

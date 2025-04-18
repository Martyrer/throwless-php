<?php

declare(strict_types=1);

use Martyrer\Throwless\Attribute\SideEffect;

test('SideEffect can be constructed with description', function (): void {
    $description = 'This method has side effects';
    $attribute = new SideEffect($description);
    expect($attribute->getDescription())->toBe($description);
});

test('SideEffect is method-only attribute', function (): void {
    $reflection = new ReflectionClass(SideEffect::class);
    $attributes = $reflection->getAttributes();

    expect($attributes)->toHaveCount(1)
        ->and($attributes[0]->getName())->toBe(Attribute::class)
        ->and($attributes[0]->getArguments())->toBe([Attribute::TARGET_METHOD]);
});

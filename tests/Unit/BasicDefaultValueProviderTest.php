<?php

declare(strict_types=1);

use Martyrer\Throwless\BasicDefaultValueProvider;

test('BasicDefaultValueProvider provides default values for basic types', function (): void {
    $provider = new BasicDefaultValueProvider();

    expect($provider->getDefaultValue('string'))->toBe('');
    expect($provider->getDefaultValue('int'))->toBe(0);
    expect($provider->getDefaultValue('float'))->toBe(0.0);
    expect($provider->getDefaultValue('bool'))->toBe(false);
    expect($provider->getDefaultValue('array'))->toBe([]);
    expect($provider->getDefaultValue('iterable'))->toBe([]);
    expect($provider->getDefaultValue('object'))->toBeInstanceOf(stdClass::class);
});

test('BasicDefaultValueProvider provides null for unknown types', function (): void {
    $provider = new BasicDefaultValueProvider();

    expect($provider->getDefaultValue('UnknownType'))->toBeNull();
});

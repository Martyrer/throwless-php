<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

use stdClass;

/**
 * Provides default values for basic PHP types.
 *
 * @template-implements DefaultValueProvider<mixed>
 */
final class BasicDefaultValueProvider implements DefaultValueProvider
{
    /**
     * Returns a default value for the given type.
     *
     * @param  string  $type  The type to provide defaults for (e.g., 'string', 'int', 'array', etc.)
     * @return mixed The default value for the type
     */
    public function getDefaultValue(string $type): mixed
    {
        return match ($type) {
            'string' => '',
            'int' => 0,
            'float' => 0.0,
            'bool' => false,
            'array' => [],
            'iterable' => [],
            'object' => new stdClass(),
            default => null,
        };
    }
}

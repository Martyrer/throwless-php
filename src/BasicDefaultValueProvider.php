<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

use ValueError;

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
        try {
            return TypeDefaults::from($type)->getDefaultValue();
        } catch (ValueError) {
            return null;
        }
    }
}

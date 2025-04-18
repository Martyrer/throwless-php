<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

/**
 * Interface for providing default values for types.
 *
 * @template T The type of the default value
 */
interface DefaultValueProvider
{
    /**
     * Returns a default value for the given type.
     *
     * @param  string  $type  The type to provide defaults for (e.g., 'string', 'int', 'array', etc.)
     * @return T The default value for the type
     */
    public function getDefaultValue(string $type): mixed;
}

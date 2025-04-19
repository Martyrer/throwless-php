<?php

declare(strict_types=1);

namespace Martyrer\Throwless;

enum TypeDefaults: string
{
    case STRING = 'string';
    case INT = 'int';
    case FLOAT = 'float';
    case BOOL = 'bool';
    case ARRAY = 'array';

    public function getDefaultValue(): mixed
    {
        return match ($this) {
            self::STRING => '',
            self::INT => 0,
            self::FLOAT => 0.0,
            self::BOOL => false,
            self::ARRAY => [],
        };
    }
}

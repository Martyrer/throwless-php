<?php

declare(strict_types=1);

namespace Martyrer\Throwless\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class ResultType
{
    public function __construct(
        public string $description
    ) {}
}

<?php

declare(strict_types=1);

namespace Martyrer\Throwless\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Pure
{
    public function __construct(
        public string $description = ''
    ) {}
}

<?php

declare(strict_types=1);

namespace Martyrer\Throwless\Attribute;

use Attribute;
use Martyrer\Throwless\Result;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class ResultReturn
{
    /**
     * @template T
     * @template E
     *
     * @param  class-string<Result<T, E>>  $expectedType  The expected Result type that should be returned
     */
    public function __construct(
        public string $expectedType = Result::class
    ) {}
}

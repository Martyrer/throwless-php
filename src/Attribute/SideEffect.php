<?php

declare(strict_types=1);

namespace Martyrer\Throwless\Attribute;

use Attribute;

/**
 * Marks a method that may have side effects through callbacks or computations.
 *
 * This is used to explicitly document that a method:
 * - May execute callbacks that could modify state
 * - May perform computations that aren't pure
 * - May have observable side effects
 * - Is not referentially transparent
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class SideEffect
{
    public function __construct(
        private string $description
    ) {}

    public function getDescription(): string
    {
        return $this->description;
    }
}

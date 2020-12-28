<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Attribute as HerodotAttribute;

#[Attribute(
    Attribute::TARGET_CLASS |
    Attribute::TARGET_METHOD |
    Attribute::TARGET_FUNCTION
)]
class Unauthenticated implements HerodotAttribute
{
    #[Pure] public function __construct(protected ?string $guard = null)
    {
    }

    #[Pure] public function getGuard(): ?string
    {
        return $this->guard;
    }
}

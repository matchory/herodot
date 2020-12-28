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
class Hidden implements HerodotAttribute
{
    public function __construct(protected ?string $reason = null)
    {
    }

    #[Pure] public function getReason(): ?string
    {
        return $this->reason;
    }
}

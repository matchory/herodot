<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes\Resources;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class ResourceShape
{
    public function __construct(protected array $shape)
    {
    }

    public function getShape(): array
    {
        return $this->shape;
    }
}

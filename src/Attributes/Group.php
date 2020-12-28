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
class Group implements HerodotAttribute
{
    use HasMetaData;

    public function __construct(
        protected string $name,
        ?array $meta = null
    ) {
        $this->setMeta($meta);
    }

    #[Pure] public function getName(): string
    {
        return $this->name;
    }
}

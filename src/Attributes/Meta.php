<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Attribute as HerodotAttribute;

#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_CLASS |
    Attribute::TARGET_METHOD |
    Attribute::TARGET_FUNCTION
)]
class Meta implements HerodotAttribute
{
    #[Pure] public function __construct(
        protected string $key,
        protected mixed $value = null
    ) {
    }

    #[Pure] public function getKey(): string
    {
        return $this->key;
    }

    #[Pure] public function getValue(): mixed
    {
        return $this->value;
    }
}

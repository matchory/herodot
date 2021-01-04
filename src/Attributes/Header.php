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
class Header implements HerodotAttribute
{
    use HasMetaData;

    public function __construct(
        protected string $name,
        protected ?string $description = null,
        protected mixed $example = null,
        ?array $meta = null
    ) {
        $this->setMeta($meta);
    }

    #[Pure] public function getName(): string
    {
        return $this->name;
    }

    #[Pure] public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Pure] public function getExample(): mixed
    {
        return $this->example;
    }
}

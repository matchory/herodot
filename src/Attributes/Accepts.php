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
class Accepts implements HerodotAttribute
{
    use HasMetaData;

    public function __construct(
        protected string $mediaType,
        protected ?string $description = null,
        ?array $meta = []
    ) {
        $this->setMeta($meta);
    }

    #[Pure] public function getMediaType(): string
    {
        return $this->mediaType;
    }

    #[Pure] public function getDescription(): ?string
    {
        return $this->description;
    }
}

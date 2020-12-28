<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use Attribute;
use Matchory\Herodot\Contracts\Attribute as HerodotAttribute;

#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_CLASS |
    Attribute::TARGET_METHOD |
    Attribute::TARGET_FUNCTION
)]
class ContentType implements HerodotAttribute
{
    use HasMetaData;

    public function __construct(
        protected string $contentType,
        protected ?string $description = null,
        ?array $meta = []
    ) {
        $this->setMeta($meta);
    }
}

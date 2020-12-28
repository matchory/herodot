<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use Attribute;
use Matchory\Herodot\Contracts\Attribute as HerodotAttribute;

#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_METHOD |
    Attribute::TARGET_FUNCTION
)]
class ResponseFile implements HerodotAttribute
{
    use HasMetaData;

    public function __construct(
        protected string $path,
        protected ?int $status = null,
        protected ?string $scenario = null,
        ?array $meta = null
    ) {
        $this->setMeta($meta);
    }
}

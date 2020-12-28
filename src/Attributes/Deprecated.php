<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use Attribute;
use Matchory\Herodot\Contracts\Attribute as HerodotAttribute;

#[Attribute(
    Attribute::TARGET_CLASS |
    Attribute::TARGET_METHOD |
    Attribute::TARGET_FUNCTION
)]
class Deprecated implements HerodotAttribute
{
    use HasMetaData;

    public function __construct(
        protected ?string $reason = null,
        protected ?string $version = null,
        ?array $meta = null
    ) {
        $this->setMeta($meta);
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }
}

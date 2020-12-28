<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Structures;

use Matchory\Herodot\Interfaces\StructureInterface;

class Deprecation implements StructureInterface
{
    final public function __construct(
        protected ?string $reason = null,
        protected ?string $version = null,
        protected ?array $meta = null
    ) {
    }

    public static function __set_state(array $array): static
    {
        return new static(
            $array['reason'] ?? null,
            $array['version'] ?? null,
            $array['meta'] ?? null
        );
    }

    public function getMeta(): ?array
    {
        return $this->meta;
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

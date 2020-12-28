<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use JetBrains\PhpStorm\Pure;

trait HasMetaData
{
    protected ?array $meta = null;

    #[Pure] public function getMeta(): ?array
    {
        return $this->meta;
    }

    protected function setMeta(?array $meta = null): void
    {
        $this->meta = $meta;
    }
}

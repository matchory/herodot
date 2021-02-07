<?php

declare(strict_types=1);

namespace Matchory\Herodot\Entities;

use Matchory\Herodot\Interfaces\StructureInterface;

class ApiManifest implements StructureInterface
{
    final public function __construct(
        protected string $name,
        protected ?string $version = null
    ) {
    }

    public static function __set_state(array $array): static
    {
        return new static($array['name'], $array['version']);
    }
}

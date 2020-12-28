<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Structures;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Interfaces\StructureInterface;

class Header implements StructureInterface
{
    protected string $name;

    protected mixed $example;

    protected ?array $meta;

    final    public function __construct(
        string $name,
        mixed $example = null,
        ?array $meta = null
    ) {
        $this->name = $name;
        $this->example = $example;
        $this->meta = $meta;
    }

    public static function __set_state(array $array): static
    {
        return new static(
            $array['name'],
            $array['example'],
            $array['meta'],
        );
    }

    #[Pure] public function getName(): string
    {
        return $this->name;
    }

    #[Pure] public function getExample(): mixed
    {
        return $this->example;
    }

    #[Pure] public function getMeta(): ?array
    {
        return $this->meta;
    }
}

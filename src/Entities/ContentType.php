<?php

declare(strict_types=1);

namespace Matchory\Herodot\Entities;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Interfaces\StructureInterface;

class ContentType implements StructureInterface
{
    protected string $name;

    protected ?string $description;

    protected ?array $meta;

    final    public function __construct(
        string $name,
        ?string $description = null,
        ?array $meta = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->meta = $meta;
    }

    public static function __set_state(array $array): static
    {
        return new static(
            $array['name'],
            $array['description'],
            $array['meta'],
        );
    }

    #[Pure] public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Pure] public function getMeta(): ?array
    {
        return $this->meta;
    }

    #[Pure] public function getName(): string
    {
        return $this->name;
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Types;

use Matchory\Herodot\Interfaces\TypeInterface;

use function implode;

abstract class TemplateType implements TypeInterface
{
    public function __construct(protected array $subTypes = [])
    {
    }

    public function getSubTypes(): array
    {
        return $this->subTypes;
    }

    public function __toString(): string
    {
        $name = $this->getName();

        if ( ! $this->subTypes) {
            return $name;
        }

        $subTypes = implode(' | ', $this->subTypes);

        return "{$name}<{$subTypes}>";
    }

    public function jsonSerialize(): string
    {
        return (string)$this;
    }
}

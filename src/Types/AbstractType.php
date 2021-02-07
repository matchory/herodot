<?php

declare(strict_types=1);

namespace Matchory\Herodot\Types;

use Matchory\Herodot\Interfaces\TypeInterface;

abstract class AbstractType implements TypeInterface
{
    /**
     * Converts the type to a string representation
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function jsonSerialize(): string
    {
        return (string)$this;
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Types;

use Matchory\Herodot\Interfaces\TypeInterface;
use Stringable;

use function implode;

class TypeDefinition implements Stringable
{
    public const TYPE_SEPARATOR = '|';

    /**
     * @var TypeInterface[]
     */
    protected array $types;

    public function __construct(TypeInterface ...$types)
    {
        $this->types = $types;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getTypesAsString(): string
    {
        return implode(self::TYPE_SEPARATOR, $this->types);
    }

    public function __toString(): string
    {
        return $this->getTypesAsString();
    }

    /**
     * @param class-string|TypeInterface $type
     *
     * @return bool
     */
    public function contains(string|TypeInterface $type): bool
    {
        foreach ($this->types as $instance) {
            if ($instance === $type || $instance::class === $type) {
                return true;
            }
        }

        return false;
    }

    public function isAny(): bool
    {
        return (
            $this->count() === 1 &&
            $this->types[0]::class === AnyType::class
        );
    }

    public function count(): int
    {
        return count($this->types);
    }

    public function first(): TypeInterface
    {
        return $this->types[0];
    }
}

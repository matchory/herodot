<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Structures;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Interfaces\StructureInterface;
use Matchory\Herodot\Support\Types\AnyType;
use Matchory\Herodot\Support\Types\TypeDefinition;

abstract class AbstractParam implements StructureInterface
{
    protected string $name;

    protected TypeDefinition $typeDefinition;

    protected ?string $description;

    protected bool $required;

    protected ?Deprecation $deprecation;

    protected bool $readOnly;

    protected bool $writeOnly;

    protected mixed $example;

    protected mixed $default;

    protected ?array $validationRules;

    protected ?array $meta;

    /**
     * AbstractParam constructor.
     *
     * @param string              $name
     * @param TypeDefinition|null $typeDefinition
     * @param string|null         $description
     * @param bool                $required
     * @param mixed|null          $default
     * @param mixed|null          $example
     * @param Deprecation|null    $deprecation
     * @param bool                $readOnly
     * @param bool                $writeOnly
     * @param array|null          $validationRules
     * @param array|null          $meta
     */
    final public function __construct(
        string $name,
        ?TypeDefinition $typeDefinition = null,
        ?string $description = null,
        bool $required = false,
        mixed $default = null,
        mixed $example = null,
        ?Deprecation $deprecation = null,
        bool $readOnly = false,
        bool $writeOnly = false,
        ?array $validationRules = null,
        ?array $meta = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->required = $required;
        $this->default = $default;
        $this->example = $example;
        $this->deprecation = $deprecation;
        $this->readOnly = $readOnly;
        $this->writeOnly = $writeOnly;
        $this->validationRules = $validationRules;
        $this->meta = $meta;

        $this->typeDefinition = $typeDefinition ?? new TypeDefinition(
                new AnyType()
            );
    }

    public static function __set_state(array $array): static
    {
        return new static(
            $array['name'],
            $array['type'],
            $array['description'],
            $array['required'],
            $array['default'],
            $array['example'],
            $array['deprecation'],
            $array['validationRules'],
            $array['meta'],
        );
    }

    #[Pure] public function getName(): string
    {
        return $this->name;
    }

    #[Pure] public function getTypeDefinition(): TypeDefinition
    {
        return $this->typeDefinition;
    }

    #[Pure] public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Pure] public function isRequired(): bool
    {
        return $this->required;
    }

    #[Pure] public function getDefault(): mixed
    {
        return $this->default;
    }

    #[Pure] public function isDeprecated(): bool
    {
        return (bool)$this->deprecation;
    }

    #[Pure] public function getDeprecation(): ?Deprecation
    {
        return $this->deprecation;
    }

    #[Pure] public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    #[Pure] public function isWriteOnly(): bool
    {
        return $this->writeOnly;
    }

    #[Pure] public function getExample(): mixed
    {
        return $this->example;
    }

    #[Pure] public function getValidationRules(): ?array
    {
        return $this->validationRules;
    }

    #[Pure] public function getMeta(): ?array
    {
        return $this->meta;
    }
}

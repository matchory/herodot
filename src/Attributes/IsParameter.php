<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use JetBrains\PhpStorm\Pure;
use LogicException;
use Matchory\Herodot\Entities\Deprecation;

trait IsParameter
{
    protected string $name;

    protected ?string $type = null;

    protected ?string $description = null;

    protected bool $required = false;

    protected bool $readOnly = false;

    protected bool $writeOnly = false;

    protected mixed $example = null;

    protected mixed $default = null;

    protected ?Deprecation $deprecation = null;

    protected ?array $validationRules = null;

    #[Pure] public function getName(): string
    {
        return $this->name;
    }

    protected function setName(string $name): void
    {
        $this->name = $name;
    }

    #[Pure] public function getType(): ?string
    {
        return $this->type;
    }

    protected function setType(?string $type): void
    {
        $this->type = $type;
    }

    #[Pure] public function getDescription(): ?string
    {
        return $this->description;
    }

    protected function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    #[Pure] public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @throws LogicException
     */
    protected function setRequired(bool $required): void
    {
        if ($required && $this->default) {
            throw new LogicException(
                'Required parameters may not have a default'
            );
        }

        $this->required = $required;
    }

    #[Pure] public function getExample(): mixed
    {
        return $this->example;
    }

    protected function setExample(mixed $example): void
    {
        $this->example = $example;
    }

    #[Pure] public function getDefault(): mixed
    {
        return $this->example;
    }

    /**
     * @param mixed $default
     *
     * @throws LogicException
     */
    protected function setDefault(mixed $default): void
    {
        if ($this->required && $default !== null) {
            throw new LogicException(
                'Required parameters may not have a default'
            );
        }

        $this->default = $default;
    }

    #[Pure] public function getDeprecation(): ?Deprecation
    {
        return $this->deprecation;
    }

    protected function setDeprecation(
        ?string $reason = null,
        ?string $version = null
    ): void {
        $this->deprecation = new Deprecation(
            $reason,
            $version,
        );
    }

    #[Pure] public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    protected function setReadOnly(bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    #[Pure] public function isWriteOnly(): bool
    {
        return $this->writeOnly;
    }

    protected function setWriteOnly(bool $writeOnly): void
    {
        $this->writeOnly = $writeOnly;
    }

    #[Pure] public function getValidationRules(): ?array
    {
        return $this->validationRules;
    }

    protected function setValidationRules(?array $validationRules): void
    {
        $this->validationRules = $validationRules;
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Types;

abstract class ModelType extends AbstractType
{
    public function __construct(
        protected string $modelClass,
        protected array $subTypes = []
    ) {
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    public function getSubTypes(): array
    {
        return $this->subTypes;
    }

    public function setSubTypes(array $subTypes): void
    {
        $this->subTypes = $subTypes;
    }
}

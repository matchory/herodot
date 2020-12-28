<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Types;

class CustomType extends TemplateType
{
    public function __construct(protected string $name, ?array $subTypes = [])
    {
        parent::__construct($subTypes ?? []);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

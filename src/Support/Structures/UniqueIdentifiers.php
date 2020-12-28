<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Structures;

use Ramsey\Uuid\Uuid;

use function implode;

trait UniqueIdentifiers
{
    private ?string $id = null;

    public function getId(): string
    {
        return $this->id;
    }

    protected function generateId(string ...$props): string
    {
        return Uuid::uuid5(
            Uuid::NAMESPACE_DNS,
            implode(array: $props),
        )->toString();
    }
}

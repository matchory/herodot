<?php

declare(strict_types=1);

namespace Matchory\Herodot\Interfaces;

interface UniquelyIdentifiableInterface
{
    public function getId(): string;
}

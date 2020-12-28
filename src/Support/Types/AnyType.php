<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Types;

class AnyType extends AbstractType
{
    public function getName(): string
    {
        return 'any';
    }
}

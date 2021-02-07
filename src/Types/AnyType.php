<?php

declare(strict_types=1);

namespace Matchory\Herodot\Types;

class AnyType extends AbstractType
{
    public function getName(): string
    {
        return 'any';
    }
}

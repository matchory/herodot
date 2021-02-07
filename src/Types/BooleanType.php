<?php

declare(strict_types=1);

namespace Matchory\Herodot\Types;

class BooleanType extends ScalarType
{
    public function getName(): string
    {
        return 'boolean';
    }
}

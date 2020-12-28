<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Types;

class BooleanType extends ScalarType
{
    public function getName(): string
    {
        return 'boolean';
    }
}

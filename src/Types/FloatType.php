<?php

declare(strict_types=1);

namespace Matchory\Herodot\Types;

class FloatType extends ScalarType
{
    public function getName(): string
    {
        return 'float';
    }
}

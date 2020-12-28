<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Types;

class IntegerType extends ScalarType
{
    public function getName(): string
    {
        return 'integer';
    }
}

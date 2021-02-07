<?php

declare(strict_types=1);

namespace Matchory\Herodot\Types;

class IntegerType extends ScalarType
{
    public function getName(): string
    {
        return 'integer';
    }
}

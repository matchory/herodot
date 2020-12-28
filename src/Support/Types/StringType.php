<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Types;

class StringType extends ScalarType
{
    public function getName(): string
    {
        return 'string';
    }
}

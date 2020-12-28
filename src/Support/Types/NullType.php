<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Types;

class NullType extends AbstractType
{
    public function getName(): string
    {
        return 'null';
    }
}

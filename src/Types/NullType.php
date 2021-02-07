<?php

declare(strict_types=1);

namespace Matchory\Herodot\Types;

class NullType extends AbstractType
{
    public function getName(): string
    {
        return 'null';
    }
}

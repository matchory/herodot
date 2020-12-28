<?php

declare(strict_types=1);

namespace Matchory\Herodot\Interfaces;

use JsonSerializable;
use Stringable;

interface TypeInterface extends Stringable, JsonSerializable
{
    public function getName(): string;
}

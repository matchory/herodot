<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components\Badges;

use Matchory\Herodot\Entities\AbstractParam;
use Matchory\Herodot\View\Components\AbstractHerodotComponent;

abstract class AbstractBadge extends AbstractHerodotComponent
{
    public function __construct(public AbstractParam $parameter)
    {
    }
}

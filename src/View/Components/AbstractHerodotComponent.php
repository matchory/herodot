<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

abstract class AbstractHerodotComponent extends Component
{
    /**
     * @var string|null
     */
    public $componentName = null;

    /**
     * @var ComponentAttributeBag|null
     */
    public $attributes = null;
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components\Badges;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Matchory\Herodot\Entities;

class Deprecated extends Component
{
    /**
     * @var string|null
     */
    public $componentName = null;

    /**
     * @var ComponentAttributeBag|null
     */
    public $attributes = null;

    public Entities\AbstractParam $parameter;

    public function __construct(Entities\AbstractParam $parameter)
    {
        $this->parameter = $parameter;
    }

    public function render(): ViewContract
    {
        return View::make('herodot::components.badges.deprecated');
    }
}

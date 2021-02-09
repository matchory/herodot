<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

class Group extends Component
{
    /**
     * @var string|null
     */
    public $componentName = null;

    /**
     * @var ComponentAttributeBag|null
     */
    public $attributes = null;

    public Collection $endpoints;

    public string $name;

    public function __construct(string $name, Collection $endpoints)
    {
        $this->name = $name;
        $this->endpoints = $endpoints;
    }

    public function render(): ViewContract
    {
        return View::make('herodot::components.group');
    }
}

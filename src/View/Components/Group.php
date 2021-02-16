<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

class Group extends AbstractHerodotComponent
{
    public function __construct(
        public string $name,
        public Collection $endpoints
    ) {
    }

    public function render(): ViewContract
    {
        return View::make('herodot::components.group');
    }
}

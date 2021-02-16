<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components\Badges;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;

class Optional extends AbstractBadge
{
    public function render(): ViewContract
    {
        return View::make('herodot::components.badges.optional');
    }
}

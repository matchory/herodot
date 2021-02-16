<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components\Badges;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Matchory\Herodot\Types\TypeDefinition;

class Type extends AbstractBadge
{
    public function render(): ViewContract
    {
        return View::make('herodot::components.badges.type');
    }

    public function typeDefinition(): TypeDefinition
    {
        return $this->parameter->getTypeDefinition();
    }

    public function typeString(): string
    {
        return $this->typeDefinition()->getTypesAsString();
    }
}

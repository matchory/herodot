<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Matchory\Herodot\Entities;

class EndpointSample extends Component
{
    /**
     * @var string|null
     */
    public $componentName = null;

    /**
     * @var ComponentAttributeBag|null
     */
    public $attributes = null;

    public Entities\Endpoint $endpoint;

    public function __construct(Entities\Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function render(): ViewContract
    {
        return View::make('herodot::components.endpoint-sample');
    }
}

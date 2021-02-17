<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Matchory\Herodot\Entities;

class EndpointSample extends AbstractHerodotComponent
{
    public function __construct(public Entities\Endpoint $endpoint)
    {
    }

    public function render(): ViewContract
    {
        return View::make('herodot::components.endpoint-sample');
    }

    public function sampleJavaScript(): string
    {
        $url = $this->endpoint->getUri();

        return "fetch({$url});";
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\View\Components;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Matchory\Herodot\Entities;

use function array_filter;
use function explode;
use function implode;
use function preg_match;

class EndpointUri extends AbstractHerodotComponent
{
    public function __construct(public Entities\Endpoint $endpoint)
    {
    }

    public function render(): ViewContract
    {
        return View::make('herodot::components.endpoint-uri');
    }

    public function methods(): string
    {
        $methods = $this->endpoint->getRequestMethods();

        return implode(' | ', $methods);
    }

    public function segments(): array
    {
        return array_filter(explode(
            '/',
            $this->endpoint->getUri()
        )) ?: [''];
    }

    public function segmentIsParam(string $segment): bool
    {
        return (bool)preg_match('/^{.+}$/', $segment);
    }
}

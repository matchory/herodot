<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Contracts\RouteProcessor;

class BeforeRouteProcessing implements Event
{
    public function __construct(
        protected RouteProcessor $processor,
        protected Collection $routes
    ) {
    }

    #[Pure] public function getProcessor(): RouteProcessor
    {
        return $this->processor;
    }

    #[Pure] public function getRoutes(): Collection
    {
        return $this->routes;
    }
}

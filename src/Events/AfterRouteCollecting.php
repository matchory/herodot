<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use Illuminate\Support\Collection;
use Matchory\Herodot\Contracts\Event;

class AfterRouteCollecting implements Event
{
    public function __construct(protected Collection $routes)
    {
    }

    public function getRoutes(): Collection
    {
        return $this->routes;
    }
}

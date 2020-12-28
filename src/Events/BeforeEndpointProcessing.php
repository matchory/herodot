<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;

class BeforeEndpointProcessing implements Event
{
    public function __construct(protected ResolvedRouteInterface $route)
    {
    }

    #[Pure] public function getRoute(): ResolvedRouteInterface
    {
        return $this->route;
    }
}

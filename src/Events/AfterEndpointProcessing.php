<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;

class AfterEndpointProcessing implements Event
{
    #[Pure] public function __construct(
        protected ResolvedRouteInterface $route,
        protected Endpoint $endpoint
    ) {
    }

    #[Pure] public function getRoute(): ResolvedRouteInterface
    {
        return $this->route;
    }

    #[Pure] public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Contracts\RouteCollector;

class BeforeRouteCollecting implements Event
{
    public function __construct(protected RouteCollector $collector)
    {
    }

    public function getCollector(): RouteCollector
    {
        return $this->collector;
    }
}

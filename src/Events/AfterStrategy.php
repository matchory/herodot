<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Contracts\ExtractionStrategy;

class AfterStrategy implements Event
{
    public function __construct(
        protected ExtractionStrategy $strategy,
        protected Endpoint $endpoint
    ) {
    }

    #[Pure] public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }

    #[Pure] public function getStrategy(): ExtractionStrategy
    {
        return $this->strategy;
    }
}

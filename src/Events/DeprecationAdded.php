<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Support\Structures\Deprecation;

class DeprecationAdded implements Event
{
    public function __construct(
        protected Deprecation $deprecation,
        protected Endpoint $endpoint
    ) {
    }

    #[Pure] public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }

    #[Pure] public function getDeprecation(): Deprecation
    {
        return $this->deprecation;
    }
}

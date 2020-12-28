<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Support\Structures\BodyParam;

class BodyParamAdded implements Event
{
    public function __construct(
        protected BodyParam $bodyParam,
        protected Endpoint $endpoint
    ) {
    }

    #[Pure] public function getBodyParam(): BodyParam
    {
        return $this->bodyParam;
    }

    #[Pure] public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }
}

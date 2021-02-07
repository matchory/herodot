<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Entities\Header;

class HeaderAdded implements Event
{
    public function __construct(
        protected Header $header,
        protected Endpoint $endpoint
    ) {
    }

    #[Pure] public function getHeader(): Header
    {
        return $this->header;
    }

    #[Pure] public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }
}

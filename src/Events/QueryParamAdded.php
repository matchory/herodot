<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Support\Structures\QueryParam;

class QueryParamAdded implements Event
{
    public function __construct(
        protected QueryParam $queryParam,
        protected Endpoint $endpoint
    ) {
    }

    #[Pure] public function getQueryParam(): QueryParam
    {
        return $this->queryParam;
    }

    #[Pure] public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }
}

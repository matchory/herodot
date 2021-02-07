<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Entities\UrlParam;

class UrlParamAdded implements Event
{
    public function __construct(
        protected UrlParam $urlParam,
        protected Endpoint $endpoint
    ) {
    }

    #[Pure] public function getUrlParam(): UrlParam
    {
        return $this->urlParam;
    }

    #[Pure] public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }
}

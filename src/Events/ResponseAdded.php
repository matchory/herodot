<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Entities\Response;

class ResponseAdded implements Event
{
    public function __construct(
        protected Response $response,
        protected Endpoint $endpoint
    ) {
    }

    #[Pure] public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }

    #[Pure] public function getResponse(): Response
    {
        return $this->response;
    }
}

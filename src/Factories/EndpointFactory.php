<?php

declare(strict_types=1);

namespace Matchory\Herodot\Factories;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint as EndpointContract;
use Matchory\Herodot\Contracts\EndpointFactory as Factory;
use Matchory\Herodot\Support\Structures\Endpoint;

class EndpointFactory implements Factory
{
    /**
     * @inheritDoc
     */
    #[Pure] public function createEndpoint(): EndpointContract
    {
        return new Endpoint();
    }
}

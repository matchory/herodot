<?php

declare(strict_types=1);

namespace Matchory\Herodot\Contracts;

use JetBrains\PhpStorm\Pure;

interface EndpointFactory
{
    /**
     * Creates a new endpoint. By overriding the default factory bound to the
     * container, you can use a custom endpoint implement.
     *
     * @return Endpoint
     */
    #[Pure] public function createEndpoint(): Endpoint;
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Contracts;

use Matchory\Herodot\Events\ProcessorErrorEncountered;
use Matchory\Herodot\Exceptions\ProcessorErrorException;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;

/**
 * Endpoint Processor
 * ==================
 * Converts route actions into endpoint instances.
 *
 * @package Matchory\Herodot\Contracts
 */
interface RouteProcessor
{
    /**
     * Processes a route and converts it into an Endpoint instance. If strict
     * mode is enabled, and an error is encountered, a `ProcessorErrorException`
     * MUST be thrown.
     * If strict mode is disabled, and an error is encountered, implementations
     * MAY emit the `ProcessorErrorEncountered` event.
     * Implementations SHOULD rely on the `EndpointFactory` instance available
     * from the IoC container to create fresh endpoints.
     *
     * @param ResolvedRouteInterface $resolvedRoute
     *
     * @return Endpoint
     * @throws ProcessorErrorException
     * @see RouteProcessor::setStrict()
     * @see ProcessorErrorEncountered
     * @see EndpointFactory
     * @see Endpoint
     */
    public function process(ResolvedRouteInterface $resolvedRoute): Endpoint;

    /**
     * Setting the processor to strict mode MUST cause any exception thrown to
     * be treated fatal, leading to a `ProcessorErrorException` being thrown.
     *
     * @param bool $strict
     *
     * @see ProcessorErrorException
     */
    public function setStrict(bool $strict): void;
}

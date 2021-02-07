<?php

declare(strict_types=1);

namespace Matchory\Herodot\Processing;

use Illuminate\Contracts\Events\Dispatcher;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\EndpointFactory;
use Matchory\Herodot\Contracts\ExtractionStrategy;
use Matchory\Herodot\Contracts\RouteProcessor as Contract;
use Matchory\Herodot\Entities\UrlParam;
use Matchory\Herodot\Events\AfterEndpointProcessing;
use Matchory\Herodot\Events\AfterStrategy;
use Matchory\Herodot\Events\BeforeEndpointProcessing;
use Matchory\Herodot\Events\BeforeStrategy;
use Matchory\Herodot\Events\ProcessorErrorEncountered;
use Matchory\Herodot\Exceptions\CircularDependencyException;
use Matchory\Herodot\Exceptions\MissingDependencyException;
use Matchory\Herodot\Exceptions\ProcessorErrorException;
use Matchory\Herodot\Extracting\StrategyGraph;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use Throwable;

use function array_diff;
use function array_map;
use function usort;

/**
 * Route Processor
 * ===============
 * Default route processor implementation.
 *
 * @package Matchory\Herodot\Support\Processing
 */
class RouteProcessor implements Contract
{
    protected bool $strict = false;

    protected StrategyGraph $strategies;

    /**
     * @param Dispatcher         $dispatcher
     * @param EndpointFactory    $endpointFactory
     * @param ExtractionStrategy ...$strategies
     */
    public function __construct(
        protected Dispatcher $dispatcher,
        protected EndpointFactory $endpointFactory,
        ExtractionStrategy ...$strategies
    ) {
        // Sort the strategies by priority before adding them to the graph. This
        // makes sure the priority is still accounted for, despite dependencies
        // potentially mixing things up.
        usort($strategies, static fn(
            ExtractionStrategy $a,
            ExtractionStrategy $b,
        ): int => $a->getPriority() <=> $b->getPriority());

        $this->strategies = new StrategyGraph();

        foreach ($strategies as $strategy) {
            $this->strategies->add($strategy);
        }
    }

    #[Pure] public function isStrict(): bool
    {
        return $this->strict;
    }

    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }

    /**
     * @inheritDoc
     * @throws CircularDependencyException
     * @throws MissingDependencyException
     */
    public function process(ResolvedRouteInterface $resolvedRoute): Endpoint
    {
        $this->dispatcher->dispatch(new BeforeEndpointProcessing(
            $resolvedRoute
        ));

        $endpoint = $this->endpointFactory->createEndpoint();
        $route = $resolvedRoute->getRoute();

        // By setting these properties before the actual request, they may be
        // overridden by strategies
        /** @psalm-suppress PossiblyInvalidArgument */
        $endpoint->setDomain($route->domain());
        $endpoint->setRouteName($route->getName());

        if ($methods = $route->methods()) {
            if (
                isset($methods[0], $methods[1]) &&
                $methods[0] === 'GET' &&
                $methods[1] === 'HEAD'
            ) {
                unset($methods[1]);
            }

            $endpoint->setRequestMethods($methods);
        }

        $endpoint->setUri($route->uri());

        foreach ($this->strategies->resolve() as $strategy) {
            $this->dispatcher->dispatch(new BeforeStrategy(
                $strategy,
                $endpoint
            ));

            try {
                $strategy->handle($resolvedRoute, $endpoint);
            } catch (Throwable $exception) {
                $this->handleError(
                    sprintf(
                        "Extraction strategy %s caused an error: %s",
                        $strategy::class,
                        $exception->getMessage()
                    ),
                    $endpoint,
                    ['strategy' => $strategy],
                    $exception
                );
            } finally {
                $this->dispatcher->dispatch(new AfterStrategy(
                    $strategy,
                    $endpoint
                ));
            }
        }

        $endpoint = $this->supplementMissingUrlParams(
            $resolvedRoute,
            $endpoint
        );

        $this->dispatcher->dispatch(new AfterEndpointProcessing(
            $resolvedRoute,
            $endpoint
        ));

        return $endpoint;
    }

    /**
     * Check the route after passing the endpoint through all strategies, then
     * collect all URL parameters featured in the route but not in the endpoint.
     * This way, we can make sure there's no URL parameter missing from the docs
     * but required for the API.
     *
     * @param ResolvedRouteInterface $resolvedRoute
     * @param Endpoint               $endpoint
     *
     * @return Endpoint
     */
    protected function supplementMissingUrlParams(
        ResolvedRouteInterface $resolvedRoute,
        Endpoint $endpoint
    ): Endpoint {
        $routeParams = $resolvedRoute->getRoute()->parameterNames();
        $existing = array_map(
            static fn(UrlParam $param) => $param->getName(),
            $endpoint->getUrlParams()
        );

        $missing = array_diff($routeParams, $existing);

        foreach ($missing as $missingParam) {
            $endpoint->addUrlParam($missingParam);
        }

        return $endpoint;
    }

    /**
     * Handles an error during processing gracefully: The app will only be
     * crashed if it is running in strict mode.
     *
     * @param string         $message
     * @param Endpoint       $endpoint
     * @param array|null     $meta
     * @param Throwable|null $previous
     *
     * @return Endpoint
     * @throws ProcessorErrorException
     */
    private function handleError(
        string $message,
        Endpoint $endpoint,
        ?array $meta = null,
        ?Throwable $previous = null
    ): Endpoint {
        $exception = new ProcessorErrorException(
            $message,
            $endpoint,
            $previous
        );

        if ($this->isStrict()) {
            throw $exception;
        }

        $this->dispatcher->dispatch(new ProcessorErrorEncountered(
            $exception,
            $meta
        ));

        return $endpoint;
    }
}


<?php

declare(strict_types=1);

namespace Matchory\Herodot\Contracts;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Exceptions\CircularDependencyException;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;

interface ExtractionStrategy
{
    /**
     * Handles information extraction by analyzing a route and modifying an
     * endpoint. Implementations MUST return the endpoint instance passed to
     * them originally, so modifications are expected to be made in-place.
     *
     * @param ResolvedRouteInterface $route
     * @param Endpoint               $endpoint
     *
     * @return Endpoint
     */
    public function handle(
        ResolvedRouteInterface $route,
        Endpoint $endpoint,
    ): Endpoint;

    /**
     * Retrieves the priority for this extraction strategy, which will be used
     * to determine which information takes precedence. Strategies with higher
     * priority will override identical information from strategies with lower
     * priority values.
     *
     * @return int
     */
    #[Pure] public function getPriority(): int;

    /**
     * Strategy dependencies make it possible to allow relying on previously
     * extracted data.
     *
     * Retrieves a list of strategy classes this strategy depends on. If it does
     * not depend on other strategies at all, it MUST return `null` or an empty
     * array.
     * Strategies MUST NOT be included multiple times. Strategies SHOULD NOT
     * form circular dependencies, as those are impossible to resolve and will
     * cause a {@see CircularDependencyException} to be thrown.
     * Order matters: Dependencies will be sorted as defined in this list.
     *
     * @return array<int, class-string<ExtractionStrategy>>|null
     */
    #[Pure] public function getDependencies(): ?array;
}

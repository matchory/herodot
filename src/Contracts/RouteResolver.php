<?php

declare(strict_types=1);

namespace Matchory\Herodot\Contracts;

use Illuminate\Routing\Route;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use ReflectionException;
use UnexpectedValueException;

interface RouteResolver
{
    /**
     * Resolves a route by reflecting on the action handler.
     *
     * @param Route $route
     *
     * @return ResolvedRouteInterface
     * @throws UnexpectedValueException
     * @throws ReflectionException
     */
    public function resolve(Route $route): ResolvedRouteInterface;
}

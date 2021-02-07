<?php

declare(strict_types=1);

namespace Matchory\Herodot\RouteCollecting;

use Closure;
use Illuminate\Routing\Route;
use JetBrains\PhpStorm\ArrayShape;
use Matchory\Herodot\Contracts\RouteResolver as Contract;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use ReflectionClass;
use ReflectionMethod;
use UnexpectedValueException;

use function explode;
use function is_array;
use function is_object;
use function is_string;
use function method_exists;

class RouteResolver implements Contract
{
    /**
     * @inheritDoc
     */
    public function resolve(Route $route): ResolvedRouteInterface
    {
        [$controller, $handler] = $this->resolveHandler($route) ?? [];

        if ( ! $controller || ! $handler) {
            throw new UnexpectedValueException('Reflection error');
        }

        $controllerReflector = new ReflectionClass($controller);
        $handlerReflector = new ReflectionMethod(
            $controller,
            $handler
        );

        return new ResolvedRoute(
            $route,
            $controllerReflector,
            $handlerReflector
        );
    }

    /**
     * @param Route $route
     *
     * @return array|null
     */
    #[ArrayShape([0 => 'class-string', 1 => 'string'])]
    protected function resolveHandler(
        Route $route
    ): ?array {
        /** @var string[] $action */
        $action = $route->getAction();

        /** @var Closure|array|string|object|null|int $uses */
        $uses = $route->getAction('uses');

        if ($uses === null) {
            return null;
        }

        if (is_array($uses)) {
            return $uses;
        }

        if (is_string($uses)) {
            return explode('@', $uses);
        }

        if (
            is_object($uses) &&
            method_exists($uses, '__invoke')
        ) {
            return [$uses, '__invoke'];
        }

        if (isset($action[0], $action[1])) {
            return [
                $action[0],
                $action[1],
            ];
        }

        return null;
    }
}

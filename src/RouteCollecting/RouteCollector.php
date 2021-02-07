<?php

declare(strict_types=1);

namespace Matchory\Herodot\RouteCollecting;

use Closure;
use Illuminate\Routing\RedirectController;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Routing\Router;
use Illuminate\Routing\ViewController;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Matchory\Herodot\Contracts\RouteCollector as Contract;

use function array_merge;
use function is_a;
use function is_array;
use function is_callable;

class RouteCollector implements Contract
{
    /**
     * @var array<string|callable|array<string|callable>>
     */
    protected array $inclusionRules = [];

    /**
     * @var array<string|callable|array<string|callable>>
     */
    protected array $exclusionRules = [
        'herodot',
        'herodot.*',
        'telescope/*',
    ];

    public function __construct(protected Router $router)
    {
    }

    /**
     * @inheritDoc
     */
    public function collect(): Collection
    {
        // Make sure we check exclusion before inclusion. Explicit removal
        // must override implicit inclusion.
        return collect($this->loadRoutes())->filter(fn(
            Route $route
        ): bool => (
            ! $this->shouldExclude($route) &&
            $this->shouldInclude($route) &&
            $this->hasValidAction($route)
        ));
    }

    /**
     * @inheritDoc
     */
    public function exclude(array $exclusionRules): void
    {
        $this->exclusionRules = array_merge(
            $this->exclusionRules,
            $exclusionRules
        );
    }

    /**
     * @inheritDoc
     */
    public function include(array $inclusionRules): void
    {
        $this->inclusionRules = array_merge(
            $this->inclusionRules,
            $inclusionRules
        );
    }

    /**
     * Retrieves the raw route collection from the application
     *
     * @return RouteCollectionInterface
     */
    protected function loadRoutes(): RouteCollectionInterface
    {
        return $this->router->getRoutes();
    }

    /**
     * Checks whether the route has a valid action that we can analyze.
     *
     * @param Route $route
     *
     * @return bool
     */
    protected function hasValidAction(Route $route): bool
    {
        /** @var Closure|null $uses */
        $uses = $route->getAction('uses');

        if ($uses instanceof Closure) {
            return true;
        }

        /** @var class-string $controller */
        $controller = $route->getController();

        if (is_a($controller, ViewController::class)) {
            return false;
        }

        if (is_a($controller, RedirectController::class)) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether to exclude a rule
     *
     * @param Route $route
     *
     * @return bool
     */
    protected function shouldExclude(Route $route): bool
    {
        foreach ($this->exclusionRules as $rule) {
            if ($this->matchesRule($route, $rule)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether to include a route
     *
     * @param Route $route
     *
     * @return bool
     */
    protected function shouldInclude(Route $route): bool
    {
        foreach ($this->inclusionRules as $rule) {
            if ($this->matchesRule($route, $rule)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether a route matches a given route. Arrays configured as rules
     * will be interpreted as "must match all" rules.
     *
     * @param Route                                  $route
     * @param string|callable|array<string|callable> $rule
     *
     * @return bool
     */
    protected function matchesRule(
        Route $route,
        callable|array|string $rule
    ): bool {
        if (is_callable($rule)) {
            return (bool)$rule($route);
        }

        if (is_array($rule)) {
            foreach ($rule as $nested) {
                if ( ! $this->matchesRule($route, $nested)) {
                    return false;
                }
            }

            return true;
        }

        return (
            (($name = $route->getName()) && Str::is($rule, $name)) ||
            Str::is($rule, $route->uri())
        );
    }
}

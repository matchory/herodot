<?php

declare(strict_types=1);

namespace Matchory\Herodot\Contracts;

use Illuminate\Support\Collection;

interface RouteCollector
{
    /**
     * Collects all routes to document. This method MUST return a collection of
     * route instances.
     *
     * @return Collection
     */
    public function collect(): Collection;

    /**
     * Configures rules for route inclusion. Rules may be specified as strings,
     * which will be checked against the route name and URI. In the rule pattern
     * an asterisk (`*`) may be used as a wildcard parameter. Callable rules, if
     * used, must accept a route instance as their only parameter, and return a
     * truthy or falsy value. Arrays are interpreted as groups of rules required
     * to to all match, other wise the check will fail.
     * Rules will be merged with prior calls to this method.
     *
     * @param array<string|callable|array<string|callable>> $inclusionRules
     */
    public function include(array $inclusionRules): void;

    /**
     * Configures rules for route exclusion. Rules may be specified as strings,
     * which will be checked against the route name and URI. In the rule pattern
     * an asterisk (`*`) may be used as a wildcard parameter. Callable rules, if
     * used, must accept a route instance as their only parameter, and return a
     * truthy or falsy value. Arrays are interpreted as groups of rules required
     * to to all match, other wise the check will fail.
     * Rules will be merged with prior calls to this method.
     *
     * @param array<string|callable|array<string|callable>> $exclusionRules
     */
    public function exclude(array $exclusionRules): void;
}

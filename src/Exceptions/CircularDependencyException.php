<?php

declare(strict_types=1);

namespace Matchory\Herodot\Exceptions;

use Exception;
use MJS\TopSort\CircularDependencyException as TopSortException;

use function implode;

/**
 * Circular Dependency Exception
 * =============================
 * Wraps around dependency resolution problems in the Herodot core. You should
 * not throw this exception as a user of Herodot.
 *
 * @package Matchory\Herodot\Exceptions
 */
class CircularDependencyException extends TopSortException
{
    /**
     * @param string[]       $nodes
     * @param Exception|null $previous
     */
    public function __construct(
        array $nodes,
        Exception $previous = null
    ) {
        $path = implode(' ➞ ', $nodes);

        parent::__construct(
            "Circular dependency: {$path}",
            0,
            $previous,
            $nodes
        );
    }
}

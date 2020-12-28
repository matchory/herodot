<?php

declare(strict_types=1);

namespace Matchory\Herodot\Exceptions;

use Exception;
use MJS\TopSort\ElementNotFoundException;

class MissingDependencyException extends ElementNotFoundException
{
    public function __construct(
        string $source,
        string $target,
        ?Exception $previous = null
    ) {
        parent::__construct(
            "Could not resolve dependencies for '{$source}': " .
            "Dependency '{$target}' is missing",
            0,
            $previous,
            $source,
            $target
        );
    }
}

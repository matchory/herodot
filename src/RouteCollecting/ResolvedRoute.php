<?php

declare(strict_types=1);

namespace Matchory\Herodot\RouteCollecting;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\File;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;

use function array_slice;
use function explode;
use function implode;
use function sha1;

use const PHP_EOL;

class ResolvedRoute implements ResolvedRouteInterface
{
    public function __construct(
        protected Route $route,
        protected ReflectionClass $controllerReflector,
        protected ReflectionFunctionAbstract $handlerReflector
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getControllerReflector(): ReflectionClass
    {
        return $this->controllerReflector;
    }

    public function getFileName(): ?string
    {
        return $this->controllerReflector->getFileName()
            ?: $this->handlerReflector->getFileName()
                ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getHandlerReflector(): ReflectionFunctionAbstract
    {
        return $this->handlerReflector;
    }

    /**
     * @inheritDoc
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function getSourceCode(): string
    {
        $reflector = $this->handlerReflector;

        // Closures require some special handling, as we can't simply derive the
        // source location from the `\Closure` class
        if ( ! $reflector->getFileName()) {
            $reflector = new ReflectionFunction(
                $this->route->getAction('uses')
            );

            // If we still can't learn anything new, bail out - this will have
            // to do. The only case I was able to make out for this happening
            // were route definitions without any action at all, so this is
            // probably fine.
            if ($reflector->getFileName() === false) {
                return '[internal]';
            }
        }

        $fileName = $reflector->getFileName();
        $startLine = $reflector->getStartLine();
        $endLine = $reflector->getEndLine();
        $controllerSource = File::get($fileName);
        $lines = explode(PHP_EOL, $controllerSource);
        $endpointSource = array_slice(
            $lines,
            $startLine - 1,
            $endLine - $startLine + 1
        );

        return implode("\n", $endpointSource);
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function getSourceHash(): string
    {
        return sha1($this->getSourceCode());
    }
}

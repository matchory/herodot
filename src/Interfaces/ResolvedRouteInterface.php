<?php

declare(strict_types=1);

namespace Matchory\Herodot\Interfaces;

use Illuminate\Routing\Route;
use ReflectionClass;
use ReflectionFunctionAbstract;

/**
 * Interface CollectedRouteInterface
 *
 * @package Matchory\Herodot\Interfaces
 */
interface ResolvedRouteInterface
{
    /**
     * Retrieves the route instance
     *
     * @return Route
     */
    public function getRoute(): Route;

    /**
     * Retrieves an SHA1 hash of the handler source to identify changes.
     *
     * @return string
     */
    public function getSourceHash(): string;

    /**
     * @return string
     */
    public function getFileName(): ?string;

    /**
     * Retrieves the source code of the handler method
     *
     * @return string
     */
    public function getSourceCode(): string;

    /**
     * Retrieves the reflection instance for the route handler class
     *
     * @return ReflectionClass
     */
    public function getControllerReflector(): ReflectionClass;

    /**
     * Retrieves the reflection instance for the route handler method or closure
     *
     * @return ReflectionFunctionAbstract
     */
    public function getHandlerReflector(): ReflectionFunctionAbstract;
}

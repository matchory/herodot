<?php

declare(strict_types=1);

namespace Matchory\Herodot;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\DocumentationWriter;
use Matchory\Herodot\Contracts\RouteCollector;
use Matchory\Herodot\Contracts\RouteProcessor;
use Matchory\Herodot\Contracts\RouteResolver;
use Matchory\Herodot\Events\AfterRouteCollecting;
use Matchory\Herodot\Events\AfterRouteProcessing;
use Matchory\Herodot\Events\AfterWriting;
use Matchory\Herodot\Events\BeforeRouteCollecting;
use Matchory\Herodot\Events\BeforeRouteProcessing;
use Matchory\Herodot\Events\BeforeWriting;
use Matchory\Herodot\Events\Complete;
use Matchory\Herodot\Exceptions\PrinterException;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface as ResolvedRoute;

use function serialize;
use function sha1;
use function unserialize;

class Generator
{
    protected bool $strict = false;

    protected bool $useCache = true;

    /**
     * @param Dispatcher          $dispatcher
     * @param RouteCollector      $collector
     * @param RouteResolver       $resolver
     * @param RouteProcessor      $processor
     * @param DocumentationWriter $writer
     */
    public function __construct(
        protected Dispatcher $dispatcher,
        protected RouteCollector $collector,
        protected RouteResolver $resolver,
        protected RouteProcessor $processor,
        protected DocumentationWriter $writer
    ) {
    }

    public function setStrict(bool $strict): void
    {
        $this->strict = $strict;
    }

    public function setUseCache(bool $useCache): void
    {
        $this->useCache = $useCache;
    }

    /**
     * @throws FileNotFoundException
     * @throws PrinterException
     */
    public function generate(): void
    {
        $routes = $this->collectRoutes();
        $endpoints = $this->processRoutes($routes);

        $this->writeOutput($endpoints);

        $this->dispatcher->dispatch(new Complete());
    }

    /**
     * @return Collection
     */
    protected function collectRoutes(): Collection
    {
        $this->dispatcher->dispatch(new BeforeRouteCollecting(
            $this->collector
        ));

        $routes = $this->collector
            ->collect()
            ->map(fn(Route $route) => $this->resolver->resolve(
                $route
            ));

        $this->dispatcher->dispatch(new AfterRouteCollecting(
            $routes
        ));

        return $routes;
    }

    /**
     * @param Collection $routes
     *
     * @return Collection
     * @throws FileNotFoundException
     */
    protected function processRoutes(Collection $routes): Collection
    {
        $hash = $this->generateCombinedHash($routes);

        // If the endpoints didn't change since the last invocation, use the
        // cached variant.
        if ($this->hasCached($hash)) {
            return $this->loadCached($hash);
        }

        $this->dispatcher->dispatch(new BeforeRouteProcessing(
            $this->processor,
            $routes
        ));

        if ($this->strict) {
            $this->processor->setStrict(true);
        }

        $endpoints = $routes
            ->map(fn(ResolvedRoute $route) => $this->processor->process(
                $route
            ))

            // By filtering the resulting collection again, we make sure to
            // remove all routes deemed unsuitable by any information extraction
            // strategy, for example a hidden attribute in the code.
            ->filter();

        // 4. Store intermediate format
        $this->storeInCache($hash, $endpoints);

        $this->dispatcher->dispatch(new AfterRouteProcessing(
            $this->processor,
            $routes,
            $endpoints
        ));

        return $endpoints;
    }

    /**
     * @param Collection $endpoints
     *
     * @throws PrinterException
     */
    protected function writeOutput(Collection $endpoints): void
    {
        $this->dispatcher->dispatch(new BeforeWriting());
        $this->writer->write($endpoints);
        $this->dispatcher->dispatch(new AfterWriting());
    }

    protected function generateCombinedHash(Collection $routes): string
    {
        $sourceHashes = $routes
            ->map(fn(ResolvedRoute $route) => $route->getSourceHash())
            ->join('');

        return sha1($sourceHashes);
    }

    /**
     * Resolves a collection hash to a cache path
     *
     * @param string $hash
     *
     * @return string
     */
    #[Pure] protected function resolveCachePath(string $hash): string
    {
        return "herodot/cache/{$hash}";
    }

    /**
     * Loads cached collection
     *
     * @param string $hash
     *
     * @return Collection
     * @throws FileNotFoundException
     * @noinspection UnserializeExploitsInspection
     */
    protected function loadCached(string $hash): Collection
    {
        $path = $this->resolveCachePath($hash);
        $data = Storage::disk('local')->get($path);

        return unserialize($data);
    }

    /**
     * Stores a collection in the cache
     *
     * @param string     $hash
     * @param Collection $collection
     *
     * @return Collection
     */
    protected function storeInCache(
        string $hash,
        Collection $collection
    ): Collection {
        $path = $this->resolveCachePath($hash);
        $data = serialize($collection);

        Storage::disk('local')->put($path, $data);

        return $collection;
    }

    /**
     * Checks whether a cached collection is available for a given hash
     *
     * @param string $hash
     *
     * @return bool
     */
    protected function hasCached(string $hash): bool
    {
        if ( ! $this->useCache) {
            return false;
        }

        $path = $this->resolveCachePath($hash);

        return Storage::disk('local')->exists($path);
    }
}

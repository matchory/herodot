<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Extraction;

use Matchory\Herodot\Contracts\ExtractionStrategy;
use Matchory\Herodot\Exceptions\CircularDependencyException;
use Matchory\Herodot\Exceptions\MissingDependencyException;
use MJS\TopSort\CircularDependencyException as TopSortCircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\ArraySort as Graph;

use function array_map;

/**
 * Extraction Strategy Graph
 * =========================
 * Forms a directed, acyclic graph to resolve inter-strategy dependencies. This
 * uses the topological sort library by marcj, which provides a pretty fast
 * implementation of a DAG algorithm.
 * The graph is only used internally and SHOULD NOT be relied upon, neither by
 * users, nor by extensions.
 *
 * @see     https://github.com/marcj/topsort.php
 * @package Matchory\Herodot\Support\Extraction
 * @internal
 */
class StrategyGraph
{
    protected Graph $graph;

    /**
     * @var ExtractionStrategy[]
     */
    private array $instances = [];

    /**
     * @var ExtractionStrategy[]
     */
    private ?array $sorted = null;

    public function __construct()
    {
        $this->graph = new Graph();
    }

    /**
     * Retrieves all nodes in the graph in the order of their addition
     *
     * @return ExtractionStrategy[]
     */
    public function getNodes(): array
    {
        return $this->instances;
    }

    /**
     * Adds a strategy to the graph. If it has any dependencies, they will be
     * added automatically.
     *
     * @param ExtractionStrategy $strategy
     *
     * @return $this
     */
    final public function add(ExtractionStrategy $strategy): static
    {
        // Invalidate cached result
        $this->sorted = null;

        // Store the instance itself separately, so we can correlate it later on
        $this->instances[$strategy::class] = $strategy;
        $this->graph->add(
            $strategy::class,
            $strategy->getDependencies() ?? []
        );

        return $this;
    }

    /**
     * Resolves the current graph to a topologically sorted array, or, more
     * technically, a directed acyclic graph.
     *
     * @return ExtractionStrategy[]
     * @throws CircularDependencyException
     * @throws MissingDependencyException
     */
    final public function resolve(): array
    {
        if ( ! $this->sorted) {
            $this->sorted = $this->sort();
        }

        return $this->sorted;
    }

    /**
     * @return ExtractionStrategy[]
     * @throws CircularDependencyException
     * @throws MissingDependencyException
     */
    private function sort(): array
    {
        try {
            // Build the sorted graph itself. This may throw, so it's singled
            // out here.
            $sorted = $this->graph->sort();

            return array_map(
                fn(string $strategy) => $this->instances[$strategy],
                $sorted
            );
        } catch (TopSortCircularDependencyException $exception) {
            throw new CircularDependencyException(
                $exception->getNodes(),
                $exception
            );
        } catch (ElementNotFoundException $exception) {
            throw new MissingDependencyException(
                $exception->getSource(),
                $exception->getTarget(),
                $exception
            );
        }
    }
}

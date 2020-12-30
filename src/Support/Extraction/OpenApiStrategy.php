<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Extraction;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\DocParser;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\ExtractionStrategy;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use OpenApi\Analysis;
use OpenApi\Annotations\Delete;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Head;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\Options;
use OpenApi\Annotations\Patch;
use OpenApi\Annotations\Post;
use OpenApi\Annotations\Put;
use OpenApi\Annotations\Trace;
use OpenApi\Context;
use ReflectionException;
use RuntimeException;

use function strtolower;

use const OpenApi\Annotations\UNDEFINED;

class OpenApiStrategy implements ExtractionStrategy
{
    protected DocParser $docParser;

    /**
     * @param DocParser $docParser
     *
     * @throws RuntimeException
     */
    public function __construct(DocParser $docParser)
    {
        $docParser->setIgnoreNotImportedAnnotations(true);
        $docParser->setImports([
            'oa' => 'OpenApi\Annotations',
        ]);

        $this->docParser = $docParser;
    }

    #[Pure] public function getDependencies(): ?array
    {
        return null;
    }

    #[Pure] public function getPriority(): int
    {
        return 90;
    }

    /**
     * @param ResolvedRouteInterface $route
     * @param Endpoint               $endpoint
     *
     * @return Endpoint
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function handle(
        ResolvedRouteInterface $route,
        Endpoint $endpoint,
    ): Endpoint {
        $annotations = $this->docParser->parse(
            $route->getHandlerReflector()->getDocComment()
        );

        $context = new Context([
            'filename' => $route->getFileName(),
            'line' => $route->getHandlerReflector()->getStartLine(),
            'class' => $route->getControllerReflector()->getName(),
            'method' => $route->getHandlerReflector()->getName(),
            'comment' => $route->getHandlerReflector()->getDocComment(),
        ]);
        $analysis = new Analysis($annotations, $context);

        foreach ($route->getRoute()->methods() as $method) {
            $annotationClass = match (strtolower($method)) {
                'get' => Get::class,
                'post' => Post::class,
                'put' => Put::class,
                'patch' => Patch::class,
                'delete' => Delete::class,
                'options' => Options::class,
                'head' => Head::class,
                'trace' => Trace::class,
                default => null
            };

            if ( ! $annotationClass) {
                continue;
            }

            /** @var Operation $annotation */
            $annotation = new $annotationClass([
                'path' => $route->getRoute()->uri(),
                'method' => $method,
            ]);

            $analysis->addAnnotation($annotation, $context);
        }

        $analysis->process();

        if ($analysis->openapi->paths === UNDEFINED) {
            return $endpoint;
        }

        foreach ($analysis->openapi->paths as $path) {
            Log::error(
                "Missing OpenAPI Annotation parsing for {$path->path}"
            );
        }

        return $endpoint;
    }
}

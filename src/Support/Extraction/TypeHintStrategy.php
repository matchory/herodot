<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Extraction;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Attributes\Resources\ResourceShape;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\ExtractionStrategy;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionUnionType;

use function is_a;
use function is_array;
use function Matchory\Herodot\resolveAttribute;

class TypeHintStrategy implements ExtractionStrategy
{
    #[Pure] public function getDependencies(): ?array
    {
        return null;
    }

    #[Pure] public function getPriority(): int
    {
        return 60;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function handle(
        ResolvedRouteInterface $route,
        Endpoint $endpoint,
    ): Endpoint {
        $returnType = $route->getHandlerReflector()->getReturnType();

        // Skip endpoints without a return type hint as this strategy can't help
        if ( ! $returnType) {
            return $endpoint;
        }

        $types = $returnType instanceof ReflectionUnionType
            ? $returnType->getTypes()
            : [$returnType];

        foreach ($types as $type) {
            $name = $type->getName();

            // Skip built-in types, we can't infer anything from them.
            // TODO: Array returns could count as JSON
            if ($type->isBuiltin()) {
                continue;
            }

            // TODO: Extract JSON content type into config
            if (is_a(
                $name,
                JsonResponse::class,
                true
            )) {
                $endpoint->addResponse(
                    null,
                    'application/json'
                );
            }

            if (is_a(
                $name,
                JsonResource::class,
                true,
            )) {
                $resourceReflection = new ReflectionClass($name);
                $shape = $this->resolveShape($resourceReflection);

                $endpoint->addResponse(
                    $shape,
                    'application/json'
                );
            }
        }

        return $endpoint;
    }

    /**
     * Try to resolve the shape of the `toArray` result of a class.To do this,
     * we check our own ResourceShape attribute or the JetBrains ArrayShape
     * attribute, if it is set. For the time being, it is only provided
     * out-of-the-box by PHPStorm, but luckily, attribute classes do not have to
     * really exist! We can even retrieve the arguments here, without ever
     * having to create an instance of the class. This extends to the `use`
     * declaration in classes, so its  super-safe to use.
     * Over time, this will probably be moved into a PSR anyway.
     *
     * @param ReflectionClass $reflection
     *
     * @return array|null
     */
    protected function resolveShape(ReflectionClass $reflection): ?array
    {
        // TODO: We could expand this to add support for similar or competing
        //       attributes as they become available
        return $this->resolveShapeFromHerodotAttribute($reflection)
               ?? $this->resolveShapeFromJetBrainsAttribute($reflection)
                  ?? null;
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return array<string, mixed>|null
     */
    protected function resolveShapeFromHerodotAttribute(
        ReflectionClass $reflection
    ): ?array {
        $resourceShape = resolveAttribute(
            $reflection,
            ResourceShape::class
        );

        if ( ! $resourceShape && $reflection->hasMethod('toArray')) {
            $toArrayReflection = $reflection->getMethod('toArray');
            $resourceShape = resolveAttribute(
                $toArrayReflection,
                ResourceShape::class
            );
        }

        if ( ! $resourceShape) {
            return null;
        }

        return is_array($resourceShape)
            ? $resourceShape[0]->getArguments()
            : $resourceShape->getArguments();
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return array<string, mixed>|null
     */
    protected function resolveShapeFromJetBrainsAttribute(
        ReflectionClass $reflection
    ): ?array {
        if ( ! $reflection->hasMethod('toArray')) {
            return null;
        }

        $toArrayReflection = $reflection->getMethod('toArray');

        /**
         * @var class-string
         * @noinspection ClassConstantCanBeUsedInspection
         */
        $attributeName = 'JetBrains\\PhpStorm\\ArrayShape';

        $arrayShape = resolveAttribute(
            $toArrayReflection,
            $attributeName
        );

        if ( ! $arrayShape) {
            return null;
        }

        return is_array($arrayShape)
            ? $arrayShape[0]->getArguments()
            : $arrayShape->getArguments();
    }
}

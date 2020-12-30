<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Extraction;

use Error;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use LogicException;
use Matchory\Herodot\Attributes\Accepts;
use Matchory\Herodot\Attributes\Authenticated;
use Matchory\Herodot\Attributes\BodyParam;
use Matchory\Herodot\Attributes\Deprecated;
use Matchory\Herodot\Attributes\Description;
use Matchory\Herodot\Attributes\Group;
use Matchory\Herodot\Attributes\Header;
use Matchory\Herodot\Attributes\Hidden;
use Matchory\Herodot\Attributes\Meta;
use Matchory\Herodot\Attributes\QueryParam;
use Matchory\Herodot\Attributes\Title;
use Matchory\Herodot\Attributes\Unauthenticated;
use Matchory\Herodot\Attributes\UrlParam;
use Matchory\Herodot\Contracts\Attribute;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\ExtractionStrategy;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use Matchory\Herodot\Support\Processing\TypeParser;
use ParseError;
use ReflectionAttribute;
use ReflectionFunctionAbstract;

use function array_search;
use function in_array;
use function is_a;
use function sprintf;

/**
 * Attribute Strategy
 * ==================
 * Extracts information by parsing attributes
 *
 * @package Matchory\Herodot\Support\Extraction
 */
class AttributeStrategy implements ExtractionStrategy
{
    public function __construct(private TypeParser $typeParser)
    {
    }

    #[Pure] public function getDependencies(): ?array
    {
        return null;
    }

    #[Pure] public function getPriority(): int
    {
        return 120;
    }

    /**
     * @inheritDoc
     * @throws ParseError
     * @throws LogicException
     */
    public function handle(
        ResolvedRouteInterface $route,
        Endpoint $endpoint,
    ): Endpoint {
        $attributes = $this->parseAttributes(
            $route->getHandlerReflector()
        );

        if ($hidden = $attributes->get(Hidden::class)[0] ?? null) {
            /** @var Hidden $hidden */
            $endpoint->setHidden(true, $hidden->getReason());
        }

        if ($deprecated = $attributes->get(Deprecated::class)[0] ?? null) {
            /** @var Deprecated $deprecated */
            $endpoint->setDeprecated(
                $deprecated->getReason(),
                $deprecated->getVersion(),
                $deprecated->getMeta()
            );
        }

        if ($title = $attributes->get(Title::class)[0] ?? null) {
            /** @var Title $title */
            $endpoint->setTitle($title->getTitle());
        }

        if ($description = $attributes->get(Description::class)[0] ?? null) {
            /** @var Description $description */
            $endpoint->setDescription($description->getDescription());
        }

        if ($group = $attributes->get(Group::class)[0] ?? null) {
            /** @var Group $group */
            $endpoint->setGroup($group->getName());
        }

        if ($acceptedTypes = $attributes->get(Accepts::class)) {
            /** @var Accepts $acceptedType */
            foreach ($acceptedTypes as $acceptedType) {
                $endpoint->addAcceptedContentType(
                    $acceptedType->getContentType(),
                    $acceptedType->getDescription(),
                    $acceptedType->getMeta()
                );
            }
        }

        /*
         * TODO: This is probably conveyed better using the Response attribute
        if ($contentTypes = $attributes->get(ContentType::class)) {
            /** @var ContentType $contentType * /
            foreach ($contentTypes as $contentType) {
                $endpoint->addResponse()
            }
        }
        */

        if ($authenticated = $attributes->get(Authenticated::class)) {
            if ($attributes->has(Unauthenticated::class)) {
                throw new LogicException(
                    'Annotating endpoints as authenticated and ' .
                    'unauthenticated at the same time has no effect. ' .
                    'Authentication status only describes a general ' .
                    'availability to unauthenticated visitors.',
                );
            }

            $endpoint->setRequiresAuthentication(true);
        }

        if ($unauthenticated = $attributes->get(Unauthenticated::class)) {
            if ($attributes->has(Authenticated::class)) {
                throw new LogicException(
                    'Annotating endpoints as authenticated and ' .
                    'unauthenticated at the same time has no effect. ' .
                    'Authentication status only describes a general ' .
                    'availability to unauthenticated visitors.',
                );
            }

            $endpoint->setRequiresAuthentication(false);
            if ($headers = $attributes->get(Header::class)) {
                /** @var Header $header */
                foreach ($headers as $header) {
                    $endpoint->addHeader(
                        $header->getName(),
                        $header->getExample(),
                        $header->getMeta()
                    );
                }
            }
        }

        if ($attributes->has(UrlParam::class)) {
            $urlParams = $attributes->get(UrlParam::class);
            $routeParams = $route->getRoute()->parameterNames();

            /** @var UrlParam $param */
            foreach ($urlParams as $param) {
                // Remove the parameter from the URL parameter list, so we can
                // safely add the left over parameters afterwards
                if (in_array(
                    $param->getName(),
                    $routeParams,
                    true
                )) {
                    unset($routeParams[array_search(
                            $param->getName(),
                            $routeParams,
                            true
                        )]);
                }

                $typeDefinition = $param->getType();
                $types = $typeDefinition
                    ? $this->typeParser->parse($typeDefinition)
                    : null;

                $endpoint->addUrlParam(
                    $param->getName(),
                    $types,
                    $param->getDescription(),
                    $param->isRequired(),
                    $param->getDefault(),
                    $param->getExample(),
                    $param->getDeprecation(),
                    $param->isReadOnly(),
                    $param->isWriteOnly(),
                    $param->getValidationRules(),
                    $param->getMeta()
                );
            }

            // All URL parameters remaining are mandatory by the route engine
            // and have probably been forgotten to add attributes for. They must
            // appear in the output documentation regardless, which is why we
            // add them here.
            foreach ($routeParams as $paramName) {
                $endpoint->addUrlParam($paramName);
            }
        }

        if ($queryParams = $attributes->get(QueryParam::class)) {
            /** @var QueryParam $param */
            foreach ($queryParams as $param) {
                $typeDefinition = $param->getType();
                $types = $typeDefinition
                    ? $this->typeParser->parse($typeDefinition)
                    : null;

                $endpoint->addQueryParam(
                    $param->getName(),
                    $types,
                    $param->getDescription(),
                    $param->isRequired(),
                    $param->getDefault(),
                    $param->getExample(),
                    $param->getDeprecation(),
                    $param->isReadOnly(),
                    $param->isWriteOnly(),
                    $param->getValidationRules(),
                    $param->getMeta()
                );
            }
        }

        if ($bodyParams = $attributes->get(BodyParam::class)) {
            /** @var BodyParam $param */
            foreach ($bodyParams as $param) {
                $typeDefinition = $param->getType();
                $types = $typeDefinition
                    ? $this->typeParser->parse($typeDefinition)
                    : null;

                $endpoint->addBodyParam(
                    $param->getName(),
                    $types,
                    $param->getDescription(),
                    $param->isRequired(),
                    $param->getDefault(),
                    $param->getExample(),
                    $param->getDeprecation(),
                    $param->isReadOnly(),
                    $param->isWriteOnly(),
                    $param->getValidationRules(),
                    $param->getMeta()
                );
            }
        }

        if ($meta = $attributes->get(Meta::class)) {
            /** @var Meta $item */
            foreach ($meta as $item) {
                $endpoint->addMeta($item->getKey(), $item->getValue());
            }
        }

        return $endpoint;
    }

    /**
     * @param ReflectionFunctionAbstract $reflector
     *
     * @return Collection
     */
    protected function parseAttributes(
        ReflectionFunctionAbstract $reflector
    ): Collection {
        $attributes = new Collection($reflector->getAttributes());

        try {
            $attributes = $attributes

                // Filter the attributes to Herodot attributes only: We're not
                // interested in other attributes. This will still include any
                // attributes defined by extensions, which must implement the
                // Herodot attribute interface.
                ->filter(fn(ReflectionAttribute $attribute) => is_a(
                    $attribute->getName(),
                    Attribute::class,
                    true
                ))

                // Reduce the attributes into a map-like structure, so we can
                // retrieve them by their name easily
                ->reduce(static function (
                    Collection $collection,
                    ReflectionAttribute $attribute
                ): Collection {
                    // By creating an instance of the attribute, its constraints
                    // will be validated for the first time, so if eg. an
                    // attribute not marked as repeatable has been used several
                    // times, an error will be thrown at this point.
                    // This is good, because we can safely assume all attributes
                    // behave as expected from this point on.
                    $instance = $attribute->newInstance();

                    return $collection->put($instance::class, [
                        ...($collection->get($instance::class) ?? []),
                        $instance,
                    ]);
                }, new Collection());
        } catch (Error $exception) {
            $message = sprintf(
                'Failed to process route attributes: %s in %s: %s',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );

            throw new ParseError(
                $message,
                $exception->getCode(),
                $exception
            );
        }

        return $attributes;
    }
}

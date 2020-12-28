<?php

declare(strict_types=1);

namespace Matchory\Herodot;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionProperty;

use function array_reduce;
use function function_exists;

/**
 * Retrieves all attributes of a reflection as an associative array
 *
 * @param ReflectionClass|ReflectionMethod|ReflectionFunction|ReflectionProperty $reflection
 *
 * @return array<class-string, ReflectionAttribute>
 */
if ( ! function_exists('Matchory\\Herodot\\resolveAttributes')) {
    function resolveAttributes(
        ReflectionClass|ReflectionMethod|ReflectionFunction|ReflectionProperty $reflection,
    ): array {
        $attributes = $reflection->getAttributes();

        return array_reduce($attributes, static function (
            array $carry,
            ReflectionAttribute $attribute
        ): array {
            $carry[$attribute->getName()] = $attribute;

            return $carry;
        }, []);
    }
}

/**
 * Attempts to resolve a single attribute from a reflection
 *
 * @param ReflectionClass|ReflectionMethod|ReflectionFunction|ReflectionProperty $reflection
 * @param class-string                                                           $name
 *
 * @return ReflectionAttribute|ReflectionAttribute[]|null
 */
if ( ! function_exists('Matchory\\Herodot\\resolveAttribute')) {
    function resolveAttribute(
        ReflectionClass|ReflectionMethod|ReflectionFunction|ReflectionProperty $reflection,
        string $name
    ): ReflectionAttribute|array|null {
        $instances = [];

        foreach ($reflection->getAttributes($name) as $attribute) {
            if ($attribute->getName() !== $name) {
                continue;
            }

            if ( ! $attribute->isRepeated()) {
                return $attribute;
            }

            $instances[] = $attribute;
        }

        return $instances;
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Processing;

use Matchory\Herodot\Interfaces\TypeInterface;
use Matchory\Herodot\Support\Types\AnyType;
use Matchory\Herodot\Support\Types\ArrayType;
use Matchory\Herodot\Support\Types\BooleanType;
use Matchory\Herodot\Support\Types\CustomType;
use Matchory\Herodot\Support\Types\FloatType;
use Matchory\Herodot\Support\Types\IntegerType;
use Matchory\Herodot\Support\Types\ModelType;
use Matchory\Herodot\Support\Types\NullType;
use Matchory\Herodot\Support\Types\StringType;
use Matchory\Herodot\Support\Types\TypeDefinition;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function preg_match;
use function preg_split;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

class TypeParser
{
    protected const REGEX_TYPE = '^(?:(?<type>(?:[^<])+$)|(?:(?<template>.+?)<(?<subtypes>.+)>))$';

    protected const REGEX_TYPES_SPLIT = '([^|]*<[^<>]*>+)|\|';

    protected static array $defaultTypes = [
        'any' => AnyType::class,
        'mixed' => AnyType::class,
        '*' => AnyType::class,
        'array' => ArrayType::class,
        'bool' => BooleanType::class,
        'boolean' => BooleanType::class,
        'float' => FloatType::class,
        'int' => IntegerType::class,
        'integer' => IntegerType::class,
        'null' => NullType::class,
        'string' => StringType::class,
        #'object' => ObjectType::class,
        #'binary' => BinaryType::class,
        #DateTime::class => DateType::class,
    ];

    protected array $types = [];

    /**
     * @var ModelType[]
     */
    protected array $modelTypes = [];

    public function __construct(array $types = [])
    {
        $this->types = array_merge(
            static::$defaultTypes,
            $types
        );
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param string $typeString
     *
     * @return TypeDefinition
     */
    public function parse(string $typeString): TypeDefinition
    {
        $types = [];
        $items = preg_split(
            '/' . static::REGEX_TYPES_SPLIT . '/',
            $typeString,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        foreach (array_unique($items) as $item) {
            $type = $this->parseTypeString($item);

            if ($type) {
                $types[] = $type;
            }
        }

        return new TypeDefinition(...$types);
    }

    /**
     * Parses a type string recursively
     *
     * @param string $typeString
     *
     * @return TypeInterface|null
     */
    protected function parseTypeString(string $typeString): ?TypeInterface
    {
        // TODO: If we want to support `type[]` syntax as an alias to
        //       `array<type>`, we should probably parse that here

        // Check if the general type syntax matches
        if ( ! preg_match(
            '/' . static::REGEX_TYPE . '/',
            $typeString,
            $matches
        )) {
            return null;
        }

        // If we have a single type, take that, otherwise use the template type
        $type = $matches['type'] ?: $matches['template'];
        $subTypes = null;

        // If we have subtypes, parse those recursively. Subtypes indicate a
        // type acts as a template, or generic type, for one or more other types
        // contained within it. Subtypes: plural, because a container can of
        // course contain multiple, different types, and just as ordinary type
        // declarations, they are split by separating them with `|` (pipe)
        // characters. So a subtype declaration might look like this:
        // `array<int|string>`. This declares the type as an array with elements
        // of type integer or string.
        if (isset($matches['subtypes'])) {
            $subTypes = $this->parse($matches['subtypes'])
                             ->getTypes();
        }

        // If this is a primitive type we support, create a new instance of that
        // type and return.
        if (array_key_exists($type, $this->types)) {
            $typeClass = $this->types[$type];

            /** @var TypeInterface $dataType */
            $dataType = new $typeClass($subTypes);

            return $dataType;
        }

        // If this is a model type we support, *clone* it and return. This is
        // necessary due the fact that we cannot simply pull completely new
        // classes out of our ass without using eval(), which I hate (my code,
        // sorry ¯\_(ツ)_/¯).
        // So instead, we've already created an instance using an anonymous
        // class, and we're cloning that one. This gives us almost identical
        // properties: There is a specific type for each entity, and it knows
        // everything it needs to about the class in question.
        if (array_key_exists($type, $this->modelTypes)) {
            $typeClass = clone $this->modelTypes[$type];

            if ($subTypes) {
                $typeClass->setSubTypes($subTypes);
            }

            return $typeClass;
        }

        // If we don't have a matching type, return a custom type
        return new CustomType($type, $subTypes);
    }
}

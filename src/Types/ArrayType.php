<?php

declare(strict_types=1);

namespace Matchory\Herodot\Types;

use function count;

class ArrayType extends TemplateType
{
    private const NAME = 'array';

    public function __toString(): string
    {
        $name = $this->getName();

        if ( ! $this->subTypes) {
            return $name;
        }

        // If we have a template type with a single subtype, we can use the
        // short notation of `type[]`
        if ($name === self::NAME && count($this->subTypes) === 1) {
            return $this->subTypes[0] . '[]';
        }

        return parent::__toString();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}

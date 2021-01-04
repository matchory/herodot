<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use Attribute;
use LogicException;
use Matchory\Herodot\Contracts\Attribute as HerodotAttribute;

#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_CLASS |
    Attribute::TARGET_METHOD |
    Attribute::TARGET_FUNCTION
)]
class UrlParam implements HerodotAttribute
{
    use IsParameter;
    use HasMetaData;

    /**
     * UrlParam constructor.
     *
     * @param string      $name
     * @param string|null $type
     * @param string|null $description
     * @param bool        $required
     * @param mixed|null  $default
     * @param mixed|null  $example
     * @param bool        $deprecated
     * @param array|null  $validationRules
     * @param array|null  $meta
     *
     * @throws LogicException
     */
    public function __construct(
        string $name,
        ?string $type = null,
        ?string $description = null,
        bool $required = true,
        mixed $default = null,
        mixed $example = null,
        bool $deprecated = false,
        ?array $validationRules = null,
        ?array $meta = null
    ) {
        $this->setName($name);
        $this->setType($type);
        $this->setDescription($description);
        $this->setRequired($required);
        $this->setDefault($default);
        $this->setExample($example);
        $this->setValidationRules($validationRules);
        $this->setMeta($meta);

        if ($deprecated) {
            $this->setDeprecation();
        }
    }
}

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
class BodyParam implements HerodotAttribute
{
    use IsParameter;
    use HasMetaData;

    /**
     * BodyParam constructor.
     *
     * @param string      $name
     * @param string|null $type
     * @param string|null $description
     * @param bool        $required
     * @param mixed|null  $default
     * @param mixed|null  $example
     * @param bool|null   $deprecated
     * @param bool        $readOnly
     * @param bool        $writeOnly
     * @param array|null  $validationRules
     * @param array|null  $meta
     *
     * @throws LogicException
     */
    public function __construct(
        string $name,
        ?string $type = null,
        ?string $description = null,
        bool $required = false,
        mixed $default = null,
        mixed $example = null,
        ?bool $deprecated = null,
        bool $readOnly = false,
        bool $writeOnly = false,
        ?array $validationRules = null,
        ?array $meta = null
    ) {
        $this->setName($name);
        $this->setType($type);
        $this->setDescription($description);
        $this->setRequired($required);
        $this->setDefault($default);
        $this->setExample($example);
        $this->setReadOnly($readOnly);
        $this->setWriteOnly($writeOnly);
        $this->setValidationRules($validationRules);
        $this->setMeta($meta);

        if ($deprecated) {
            $this->setDeprecation();
        }
    }
}

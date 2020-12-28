<?php

declare(strict_types=1);

namespace Matchory\Herodot\Attributes;

use Attribute;

#[Attribute(
    Attribute::IS_REPEATABLE |
    Attribute::TARGET_CLASS |
    Attribute::TARGET_METHOD |
    Attribute::TARGET_FUNCTION
)]
class QueryParam
{
    use IsParameter;
    use HasMetaData;

    public function __construct(
        string $name,
        ?string $type = null,
        ?string $description = null,
        bool $required = false,
        mixed $default = null,
        mixed $example = null,
        ?bool $deprecated = null,
        ?string $deprecatedSince = null,
        ?string $deprecationReason = null,
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
            $this->setDeprecation(
                $deprecationReason,
                $deprecatedSince
            );
        }
    }
}

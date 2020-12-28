<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Structures;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Interfaces\StructureInterface;

class Response implements StructureInterface
{
    public const DEFAULT_CONTENT_TYPE = 'text/plain';

    public const DEFAULT_SCENARIO = 'default';

    public const DEFAULT_STATUS = 200;

    /**
     * @var mixed
     */
    protected $example;

    protected string $contentType;

    protected int $status;

    protected string $scenario;

    protected ?array $meta;

    final public function __construct(
        mixed $example,
        ?string $contentType = null,
        ?int $status = null,
        ?string $scenario = null,
        ?array $meta = null
    ) {
        $this->example = $example;
        $this->contentType = $contentType ?? self::DEFAULT_CONTENT_TYPE;
        $this->status = $status ?? self::DEFAULT_STATUS;
        $this->scenario = $scenario ?? self::DEFAULT_SCENARIO;
        $this->meta = $meta;
    }

    public static function __set_state(array $array): static
    {
        return new static(
            $array['example'],
            $array['contentType'],
            $array['status'],
            $array['scenario'],
            $array['meta'],
        );
    }

    #[Pure] public function getMeta(): ?array
    {
        return $this->meta;
    }

    #[Pure] public function getContentType(): string
    {
        return $this->contentType;
    }

    #[Pure] public function getExample(): mixed
    {
        return $this->example;
    }

    #[Pure] public function getScenario(): string
    {
        return $this->scenario;
    }

    #[Pure] public function getStatus(): int
    {
        return $this->status;
    }
}

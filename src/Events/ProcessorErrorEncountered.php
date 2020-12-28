<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Exceptions\ProcessorErrorException;

/**
 * Processor Error Encountered
 * ===========================
 * Signals an error during processing the application source code. This is an
 * internal event that should not be used, called, or relied on outside the
 * Herodot source code. It is not part of its API and may be changed or removed
 * at any time.
 * This event allows to emit errors encountered while processing source code in
 * non-strict mode to the console command, so it can be printed to the screen.
 * This may seem unnecessary, but it allows to decouple endpoint processing from
 * the console output.
 *
 * @package Matchory\Herodot\Events
 * @internal
 */
class ProcessorErrorEncountered implements Event
{
    public function __construct(
        protected ProcessorErrorException $exception,
        protected ?array $meta = null
    ) {
    }

    #[Pure] public function getException(): ProcessorErrorException
    {
        return $this->exception;
    }

    #[Pure] public function getMeta(): ?array
    {
        return $this->meta;
    }
}

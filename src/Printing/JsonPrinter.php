<?php

declare(strict_types=1);

namespace Matchory\Herodot\Printing;

use Illuminate\Support\Collection;
use Matchory\Herodot\Contracts\Printer;

/**
 * JSON Printer
 * ============
 * Generates documentation as a JSON file
 *
 * @package Matchory\Herodot\Support\Printing
 */
class JsonPrinter implements Printer
{
    /**
     * @inheritDoc
     */
    public function print(Collection $endpoints): void
    {
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Contracts;

use Illuminate\Support\Collection;
use Matchory\Herodot\Exceptions\PrinterException;

interface DocumentationWriter
{
    /**
     * Writes the documentation using all configured printers.
     *
     * @param Collection $endpoints
     *
     * @throws PrinterException
     */
    public function write(Collection $endpoints): void;
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Contracts;

use Illuminate\Support\Collection;
use Matchory\Herodot\Exceptions\PrinterException;

interface Printer
{
    /**
     * Takes a given collection of endpoints and prints the documentation to any
     * format specific to the printer. Printers are the last step in the
     * generation pipeline and are responsible for writing any output files to
     * the disk.
     *
     * @param Collection $endpoints
     *
     * @throws PrinterException
     */
    public function print(Collection $endpoints): void;
}

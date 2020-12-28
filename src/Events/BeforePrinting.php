<?php

declare(strict_types=1);

namespace Matchory\Herodot\Events;

use Illuminate\Support\Collection;
use Matchory\Herodot\Contracts\Event;
use Matchory\Herodot\Contracts\Printer;

class BeforePrinting implements Event
{
    public function __construct(
        protected Printer $printer,
        protected Collection $endpoints
    ) {
    }

    public function getEndpoints(): Collection
    {
        return $this->endpoints;
    }

    public function getPrinter(): Printer
    {
        return $this->printer;
    }
}

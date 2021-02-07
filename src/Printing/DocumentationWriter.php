<?php

declare(strict_types=1);

namespace Matchory\Herodot\Printing;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Matchory\Herodot\Contracts\DocumentationWriter as Contract;
use Matchory\Herodot\Contracts\Printer;
use Matchory\Herodot\Events\AfterPrinting;
use Matchory\Herodot\Events\BeforePrinting;
use Matchory\Herodot\Exceptions\PrinterException;

class DocumentationWriter implements Contract
{
    protected Dispatcher $dispatcher;

    /**
     * @var Printer[]
     */
    protected array $printers;

    public function __construct(Dispatcher $dispatcher, Printer ...$printers)
    {
        $this->printers = $printers;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     * @throws PrinterException
     */
    public function write(Collection $endpoints): void
    {
        foreach ($this->printers as $printer) {
            $this->dispatcher->dispatch(new BeforePrinting(
                $printer,
                $endpoints
            ));

            $printer->print($endpoints);

            $this->dispatcher->dispatch(new AfterPrinting(
                $printer,
                $endpoints
            ));
        }
    }
}

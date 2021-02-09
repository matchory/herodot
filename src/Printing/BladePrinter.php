<?php

declare(strict_types=1);

namespace Matchory\Herodot\Printing;

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Matchory\Herodot\Contracts\Printer;
use Matchory\Herodot\Entities\Endpoint;

class BladePrinter implements Printer
{
    protected array $config;

    public function __construct(Repository $config)
    {
        $this->config = $config->get('herodot.blade', []);
    }

    /**
     * @inheritDoc
     */
    public function print(Collection $endpoints): void
    {
        $groups = $this->group($endpoints);
        $output = View
            ::make('herodot::index', [
                'groups' => $groups,
            ])
            ->render();

        Storage::disk('local')->put(
            $this->outputPath(),
            $output
        );
    }

    protected function group(Collection $endpoints): Collection
    {
        return $endpoints
            ->groupBy(fn(Endpoint $endpoint) => $endpoint->getGroup())
            ->sort();
    }

    protected function outputPath(): string
    {
        return $this->config['output_file'] ?? 'herodot/index.html';
    }
}

<?php
/** @noinspection PhpMissingFieldTypeInspection */

declare(strict_types=1);

namespace Matchory\Herodot\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Config;
use Matchory\Herodot\Contracts\DocumentationWriter;
use Matchory\Herodot\Contracts\RouteCollector;
use Matchory\Herodot\Contracts\RouteProcessor;
use Matchory\Herodot\Contracts\RouteResolver;
use Matchory\Herodot\Exceptions\PrinterException;
use Matchory\Herodot\Generator;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class GenerateCommand extends Command
{
    public const OPTION_FORCE = 'force';

    public const OPTION_FORCE_SHORTCUT = 'f';

    public const OPTION_STRICT = 'strict';

    public const OPTION_STRICT_SHORTCUT = 's';

    protected $name = 'herodot:generate';

    protected $description = 'Generates API documentation';

    /**
     * @param Dispatcher          $dispatcher
     * @param RouteCollector      $collector
     * @param RouteResolver       $resolver
     * @param RouteProcessor      $processor
     * @param DocumentationWriter $writer
     *
     * @throws FileNotFoundException
     * @throws PrinterException
     */
    public function handle(
        Dispatcher $dispatcher,
        RouteCollector $collector,
        RouteResolver $resolver,
        RouteProcessor $processor,
        DocumentationWriter $writer
    ): void {
        $collector->include(Config::get(
            'herodot.include',
            []
        ));

        $collector->exclude(Config::get(
            'herodot.exclude',
            []
        ));

        $herodot = new Generator(
            $dispatcher,
            $collector,
            $resolver,
            $processor,
            $writer
        );

        if ($this->option(self::OPTION_STRICT)) {
            $herodot->setStrict(true);
        }

        if ($this->option(self::OPTION_FORCE)) {
            $herodot->setUseCache(false);
        }

        $herodot->generate();
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->addOption(
            self::OPTION_FORCE,
            self::OPTION_FORCE_SHORTCUT,
            InputOption::VALUE_NONE,
            'Force fresh generation'
        );

        $this->addOption(
            self::OPTION_STRICT,
            self::OPTION_STRICT_SHORTCUT,
            InputOption::VALUE_NONE,
            'Abort on errors'
        );
    }
}

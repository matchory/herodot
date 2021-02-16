<?php

declare(strict_types=1);

namespace Matchory\Herodot;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverterInterface;
use LogicException;
use Matchory\Herodot\Console\Commands\GenerateCommand;
use Matchory\Herodot\Console\Commands\OverrideCommand;
use Matchory\Herodot\Contracts\DocumentationWriter as WriterContract;
use Matchory\Herodot\Contracts\EndpointFactory as FactoryContract;
use Matchory\Herodot\Contracts\ExtractionStrategy;
use Matchory\Herodot\Contracts\Printer;
use Matchory\Herodot\Contracts\RouteCollector as CollectorContract;
use Matchory\Herodot\Contracts\RouteProcessor as ProcessorContract;
use Matchory\Herodot\Contracts\RouteResolver as ResolverContract;
use Matchory\Herodot\Factories\EndpointFactory;
use Matchory\Herodot\Printing\DocumentationWriter;
use Matchory\Herodot\Processing\RouteProcessor;
use Matchory\Herodot\RouteCollecting\RouteCollector;
use Matchory\Herodot\RouteCollecting\RouteResolver;
use Matchory\Herodot\View\Components\Badges\Deprecated;
use Matchory\Herodot\View\Components\Badges\Optional;
use Matchory\Herodot\View\Components\Badges\Required;
use Matchory\Herodot\View\Components\Badges\Type;
use Matchory\Herodot\View\Components\Endpoint;
use Matchory\Herodot\View\Components\EndpointHeader;
use Matchory\Herodot\View\Components\EndpointInfo;
use Matchory\Herodot\View\Components\EndpointSample;
use Matchory\Herodot\View\Components\EndpointTitle;
use Matchory\Herodot\View\Components\EndpointUri;
use Matchory\Herodot\View\Components\Group;
use Matchory\Herodot\View\Components\Page;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;

/**
 * DocumentationServiceProvider
 *
 * @package Matchory\Herodot
 */
class HerodotServiceProvider extends ServiceProvider
{
    public const TAG_CONFIG = 'config';

    public const TAG_PUBLIC = 'public';

    public const TAG_VIEWS = 'views';

    /**
     * @throws LogicException
     */
    public function boot(): void
    {
        $this->loadViews();

        if ( ! $this->app->runningInConsole()) {
            return;
        }

        $this->publishAssets();
        $this->bindCommands();
        $this->bindImplementations();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/herodot.php',
            'herodot'
        );

        $this->bindRoutes();
    }

    /**
     * Add docs routes for users that want their docs to pass through their app.
     */
    protected function bindRoutes(): void
    {
        if (Config::get('herodot.laravel.add_routes', true)) {
            $this->loadRoutesFrom(
                __DIR__ . '/../routes/herodot.php'
            );
        }
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views/',
            'herodot'
        );

        $this->loadViewComponentsAs('herodot', [
            Page::class,
            Group::class,
            Endpoint::class,
            EndpointHeader::class,
            EndpointTitle::class,
            EndpointUri::class,
            EndpointInfo::class,
            EndpointSample::class,
            Deprecated::class,
            Required::class,
            Optional::class,
            Type::class,
        ]);
    }

    private function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => $this->app->resourcePath('views/vendor/matchory'),
        ], self::TAG_VIEWS);

        $this->publishes([
            __DIR__ . '/../public' => $this->app->basePath('public/vendor/matchory'),
        ], self::TAG_PUBLIC);

        $this->publishes([
            __DIR__ . '/../config/herodot.php' => $this->app->configPath('herodot.php'),
        ], self::TAG_CONFIG);
    }

    private function bindCommands(): void
    {
        $this->commands([
            GenerateCommand::class,
            OverrideCommand::class,
        ]);
    }

    /**
     * @throws LogicException
     */
    private function bindImplementations(): void
    {
        $this->app->bind(CollectorContract::class, Config::get(
            'herodot.route_collector',
            RouteCollector::class
        ));

        $this->app->bind(ResolverContract::class, Config::get(
            'herodot.route_resolver',
            RouteResolver::class
        ));

        $this->app->bind(ProcessorContract::class, Config::get(
            'herodot.route_processor',
            RouteProcessor::class
        ));

        $this->app->bind(FactoryContract::class, Config::get(
            'herodot.endpoint_factory',
            EndpointFactory::class
        ));

        $this->app->bind(WriterContract::class, Config::get(
            'herodot.documentation_writer',
            DocumentationWriter::class
        ));

        // As the default doc block factory requires `createInstance` to be
        // called, we bind that invocation here.
        $this->app->bind(
            DocBlockFactory::class,
            fn() => DocBlockFactory::createInstance()
        );

        // Here, we bind the doc block factory interface to the default
        // implementation we configured above. This ensures users can bring
        // their own implementation of the interface, but have a working default
        // in place, which falls back gracefully.
        $this->app->bind(
            DocBlockFactoryInterface::class,
            Config::get(
                'herodot.doc_block_factory',
                DocBlockFactory::class
            )
        );

        $this->app->singleton(
            'herodot.markdown.environment',
            function () {
                $environment = Environment::createGFMEnvironment();

                // Allow users to override the environment configuration
                $environment->mergeConfig(Config::get(
                    'herodot.markdown',
                    []
                ));

                $environment->addExtension(
                    new HeadingPermalinkExtension()
                );

                return $environment;
            }
        );

        $this->app->singleton(
            'herodot.markdown',
            fn() => new CommonMarkConverter(
                [],
                $this->app->get('herodot.markdown.environment'),
            )
        );

        $this->app->alias(
            'herodot.markdown',
            MarkdownConverterInterface::class
        );

        // Ensure the default route processor receives all configured extraction
        // strategies. Custom route processor implementations will have to take
        // care of that by themselves.
        $this->app
            ->when(RouteProcessor::class)
            ->needs(ExtractionStrategy::class)
            ->give(Config::get(
                'herodot.strategies',
                []
            ));

        $this->app
            ->when(DocumentationWriter::class)
            ->needs(Printer::class)
            ->give(Config::get(
                'herodot.printers',
                []
            ));
    }
}

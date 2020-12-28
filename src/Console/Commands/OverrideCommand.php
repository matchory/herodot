<?php
/** @noinspection PhpMissingFieldTypeInspection */

declare(strict_types=1);

namespace Matchory\Herodot\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use LogicException;
use Matchory\Herodot\HerodotServiceProvider;
use PhpParser\Lexer\Emulative as Lexer;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract as Visitor;
use PhpParser\Parser\Php7 as Php7Parser;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;
use Throwable;

use function str_replace;

class OverrideCommand extends Command
{
    protected $signature = 'herodot:override
                            {route : Name of the route to override}';

    protected $description = 'Adds a new route override section to the configuration file';

    /**
     * @param Router     $router
     * @param Filesystem $filesystem
     *
     * @return int
     * @throws LogicException
     * @throws FileNotFoundException
     */
    public function handle(
        Router $router,
        Filesystem $filesystem,
    ): int {
        /** @var string $routeName */
        $routeName = $this->argument('route');
        $route = $router->getRoutes()->getByName($routeName);

        if ( ! $route) {
            $this->error("Could not find route '{$routeName}'");

            return 1;
        }

        $configFilePath = $this->laravel->configPath('herodot.php');

        if ( ! $filesystem->exists($configFilePath)) {
            $this->error(
                'Herodot Configuration file not found. Publish it ' .
                'using the following command:'
            );

            $escapedProvider = str_replace(
                '\\',
                '\\\\',
                HerodotServiceProvider::class
            );
            $this->error(
                'php artisan vendor:publish ' .
                '--provider=' . $escapedProvider . ' ' .
                '--tag=' . HerodotServiceProvider::TAG_CONFIG
            );

            return 1;
        }

        // Read the source code
        $code = $filesystem->get($configFilePath);

        $lexer = new Lexer([
            'usedAttributes' => [
                'comments',
                'startLine',
                'endLine',
                'startTokenPos',
                'endTokenPos',
            ],
        ]);
        $parser = new Php7Parser($lexer);

        try {
            $oldStatements = $parser->parse($code);
            $oldTokens = $lexer->getTokens();
        } catch (Throwable $exception) {
            $this->error(
                'Configuration could not be updated: ' .
                $exception->getMessage()
            );

            return 1;
        }

        if ($oldStatements === null) {
            $this->error('Unknown error parsing config file');

            return 1;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class($route) extends Visitor {
            public function __construct(private Route $route)
            {
            }

            public function leaveNode(Node $node): ?Node
            {
                if (
                    $node instanceof Expr\ArrayItem &&
                    $node->key instanceof Scalar\String_ &&
                    $node->key->value === 'overrides' &&
                    $node->value instanceof Expr\Array_
                ) {
                    foreach ($node->value->items as $existing) {
                        if (
                            ! $existing instanceof Expr\ArrayItem ||
                            ! $existing->key instanceof Scalar\String_
                        ) {
                            continue;
                        }

                        if ($existing->key->value === $this->route->getName()) {
                            throw new RuntimeException(
                                "Found existing override for " .
                                "'{$this->route->getName()}' on " .
                                "line {$existing->key->getStartLine()}"
                            );
                        }
                    }

                    $overrideNode = new Expr\Array_();
                    $overrideNode->setAttribute(
                        'kind',
                        Expr\Array_::KIND_SHORT
                    );

                    $node->value->items[] = new Expr\ArrayItem(
                        $overrideNode,
                        new Scalar\String_(
                            $this->route->getName() ?? ''
                        )
                    );

                    return $node;
                }

                return null;
            }
        });

        try {
            $newStatements = $traverser->traverse($oldStatements);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $printer = new Standard ();
        $result = $printer->printFormatPreserving(
            $newStatements,
            $oldStatements,
            $oldTokens
        );

        $filesystem->put($configFilePath, $result);

        return 0;
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Extraction;

use Illuminate\Config\Repository as Config;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\ExtractionStrategy;
use Matchory\Herodot\Interfaces\ResolvedRouteInterface;
use Matchory\Herodot\Interfaces\StructureInterface;
use Matchory\Herodot\Support\Structures\Deprecation;

use function array_key_exists;
use function is_bool;
use function is_string;

class OverrideStrategy implements ExtractionStrategy
{
    public const OVERRIDE_ACCEPTS = 'accepts';

    public const OVERRIDE_AUTHENTICATED = 'authenticated';

    public const OVERRIDE_BODY_PARAMS = 'body_params';

    public const OVERRIDE_DEPRECATED = 'deprecated';

    public const OVERRIDE_DESCRIPTION = 'description';

    public const OVERRIDE_GROUP = 'group';

    public const OVERRIDE_HEADERS = 'headers';

    public const OVERRIDE_HIDDEN = 'hidden';

    public const OVERRIDE_META = 'meta';

    public const OVERRIDE_QUERY_PARAMS = 'query_params';

    public const OVERRIDE_RESPONSES = 'responses';

    public const OVERRIDE_TITLE = 'title';

    public const OVERRIDE_URL_PARAMS = 'url_params';

    /**
     * @var array<string, array<string, boolean|string|StructureInterface>>
     */
    protected array $config;

    public function __construct(Config $config)
    {
        $this->config = $config->get('herodot.overrides', []);
    }

    #[Pure] public function getDependencies(): ?array
    {
        return null;
    }

    #[Pure] public function getPriority(): int
    {
        return 999;
    }

    public function handle(
        ResolvedRouteInterface $route,
        Endpoint $endpoint
    ): Endpoint {
        $name = $route->getRoute()->getName();

        if ( ! $name || ! isset($this->config[$name])) {
            return $endpoint;
        }

        $config = $this->config[$name];

        if (array_key_exists(self::OVERRIDE_HIDDEN, $config)) {
            $hidden = (bool)$config[self::OVERRIDE_HIDDEN];
            $endpoint->setHidden($hidden);
        }

        if (array_key_exists(self::OVERRIDE_DEPRECATED, $config)) {
            $deprecation = $config[self::OVERRIDE_DEPRECATED];

            if (is_bool($deprecation)) {
                $endpoint->setDeprecated();
            } elseif (is_string($deprecation)) {
                $endpoint->setDeprecated($deprecation);
            } elseif ($deprecation instanceof Deprecation) {
                $endpoint->setDeprecated(
                    $deprecation->getReason(),
                    $deprecation->getVersion(),
                    $deprecation->getMeta()
                );
            }
        }

        if (
            array_key_exists(self::OVERRIDE_TITLE, $config) &&
            is_string($title = $config[self::OVERRIDE_TITLE])
        ) {
            $endpoint->setDescription($title);
        }

        if (
            array_key_exists(self::OVERRIDE_DESCRIPTION, $config) &&
            is_string($description = $config[self::OVERRIDE_DESCRIPTION])
        ) {
            $endpoint->setDescription($description);
        }

        if (
            array_key_exists(self::OVERRIDE_GROUP, $config) &&
            is_string($group = $config[self::OVERRIDE_GROUP])
        ) {
            $endpoint->setGroup($group);
        }

        if (array_key_exists(self::OVERRIDE_AUTHENTICATED, $config)) {
            $endpoint->setRequiresAuthentication(
                (bool)$config[self::OVERRIDE_AUTHENTICATED]
            );
        }

        return $endpoint;
    }
}

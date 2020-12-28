<?php

declare(strict_types=1);

namespace Matchory\Herodot\Support\Printing;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Printer;
use Matchory\Herodot\Interfaces\TypeInterface;
use Matchory\Herodot\Support\Structures\AbstractParam;
use Matchory\Herodot\Support\Structures\BodyParam;
use Matchory\Herodot\Support\Structures\QueryParam;
use Matchory\Herodot\Support\Structures\UrlParam;
use Symfony\Component\Yaml\Yaml;

use function array_map;
use function function_exists;

/**
 * YAML Printer
 * ============
 * Generates documentation as a YAML file
 *
 * @package Matchory\Herodot\Support\Printing
 */
class YamlPrinter implements Printer
{
    /**
     * @inheritDoc
     */
    public function print(Collection $endpoints): void
    {
        $grouped = $endpoints
            ->mapToGroups(fn(Endpoint $endpoint) => [
                $endpoint->getGroup() ?? '' => $this->serializeEndpoint(
                    $endpoint
                ),
            ])
            ->toArray();

        $output = $this->dump($grouped);

        echo $output;
    }

    protected function dump(iterable $data): string
    {
        // Use the extension function if available, fall back to Symfony Yaml
        return function_exists('yaml_emit')
            ? yaml_emit($data)
            : Yaml::dump(
                $data,
                2,
                4,
                Yaml::DUMP_NULL_AS_TILDE
            );
    }

    /**
     * @param Endpoint $endpoint
     *
     * @return array
     */
    #[ArrayShape([
        'title' => 'null|string',
        'description' => 'null|string',
        'uri' => 'string',
    ])]
    protected function serializeEndpoint(
        Endpoint $endpoint
    ): array {
        return [
            'title' => $endpoint->getTitle(),
            'description' => $endpoint->getDescription(),
            'uri' => $endpoint->getUri(),
            'url_params' => array_map(
                fn(UrlParam $param) => $this->serializeParameter(
                    $param
                ),
                $endpoint->getUrlParams()
            ),
            'query_params' => array_map(
                fn(QueryParam $param) => $this->serializeParameter(
                    $param
                ),
                $endpoint->getQueryParams()
            ),
            'body_params' => array_map(
                fn(BodyParam $param) => $this->serializeParameter(
                    $param
                ),
                $endpoint->getBodyParams()
            ),
            'meta' => $endpoint->getMeta(),
            'methods' => $endpoint->getRequestMethods(),
        ];
    }

    /**
     * @param AbstractParam $param
     *
     * @return array
     */
    #[ArrayShape([
        'name' => "string",
        'description' => "string|null",
        'types' => "string[]",
        'validation_rules' => "array|null",
        'example' => "mixed",
        'meta' => "array|null",
    ])] private function serializeParameter(
        AbstractParam $param
    ): array {
        return [
            'name' => $param->getName(),
            'description' => $param->getDescription(),
            'types' => array_map(
                static fn(TypeInterface $type) => (string)$type,
                $param->getTypeDefinition()
            ),
            'validation_rules' => $param->getValidationRules(),
            'example' => $param->getExample(),
            'meta' => $param->getMeta(),
        ];
    }
}

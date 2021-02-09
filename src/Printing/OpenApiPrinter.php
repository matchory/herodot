<?php

declare(strict_types=1);

namespace Matchory\Herodot\Printing;

use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\spec\Contact;
use cebe\openapi\spec\Encoding;
use cebe\openapi\spec\Header as OAHeader;
use cebe\openapi\spec\Info;
use cebe\openapi\spec\License;
use cebe\openapi\spec\MediaType;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\Parameter;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\Paths;
use cebe\openapi\spec\RequestBody;
use cebe\openapi\spec\Response as OAResponse;
use cebe\openapi\spec\Responses;
use cebe\openapi\spec\Schema;
use cebe\openapi\spec\Server;
use cebe\openapi\spec\Tag;
use cebe\openapi\Writer;
use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use Matchory\Herodot\Contracts\Printer;
use Matchory\Herodot\Entities\BodyParam;
use Matchory\Herodot\Entities\ContentType;
use Matchory\Herodot\Entities\Header;
use Matchory\Herodot\Entities\QueryParam;
use Matchory\Herodot\Entities\Response;
use Matchory\Herodot\Entities\UrlParam;
use Matchory\Herodot\Exceptions\PrinterException;
use Matchory\Herodot\Interfaces\TypeInterface;
use Matchory\Herodot\Types\AnyType;
use Matchory\Herodot\Types\BooleanType;
use Matchory\Herodot\Types\FloatType;
use Matchory\Herodot\Types\IntegerType;
use Matchory\Herodot\Types\ModelType;
use Matchory\Herodot\Types\NullType;
use Matchory\Herodot\Types\StringType;
use Matchory\Herodot\Types\TemplateType;
use Matchory\Herodot\Types\TypeDefinition;

use function array_map;
use function array_reduce;
use function array_unique;
use function assert;
use function count;
use function implode;
use function in_array;
use function ltrim;
use function sha1;
use function strtolower;
use function tap;

class OpenApiPrinter implements Printer
{
    public const CONFIG_API_URL = 'api_url';

    public const CONFIG_CONTACT = 'contact';

    public const CONFIG_DEFAULT_GROUP = 'default_group';

    public const CONFIG_DESCRIPTION = 'description';

    public const CONFIG_LICENSE = 'license';

    public const CONFIG_OUTPUT_FILE = 'output_file';

    public const CONFIG_OUTPUT_FORMAT = 'output_format';

    public const CONFIG_TERMS_OF_SERVICE = 'terms_of_service';

    public const CONFIG_TITLE = 'title';

    public const CONFIG_VERSION = 'version';

    public const OPEN_API_SPEC_VERSION = '3.0.2';

    public const OUTPUT_FORMAT_JSON = 'json';

    public const OUTPUT_FORMAT_YAML = 'yaml';

    public const SUPPORTED_REQUEST_METHODS = [
        'get',
        'put',
        'post',
        'delete',
        'options',
        'head',
        'patch',
        'trace',
    ];

    protected array $config;

    public function __construct(Repository $config)
    {
        // Yup, this is intentional and merges the config with the defaults
        $this->config = $config->get(
                'herodot.open_api',
                []
            ) + $this->defaultConfig();
    }

    /**
     * @param OpenApi $spec
     *
     * @throws PrinterException
     */
    public function dump(OpenApi $spec): void
    {
        $outputFile = Storage::disk('local')->path(
            $this->config[self::CONFIG_OUTPUT_FILE]
        );

        try {
            switch ($this->config[self::CONFIG_OUTPUT_FORMAT]) {
                case self::OUTPUT_FORMAT_JSON:
                    Writer::writeToJsonFile($spec, $outputFile);
                    break;

                case self::OUTPUT_FORMAT_YAML:
                default:
                    Writer::writeToYamlFile($spec, $outputFile);
            }
        } catch (IOException $exception) {
            throw new PrinterException(
                $exception->getMessage(),
                null,
                $exception
            );
        }
    }

    /**
     * @param Collection $endpoints
     *
     * @throws TypeErrorException
     * @throws PrinterException
     */
    public function print(Collection $endpoints): void
    {
        $grouped = $endpoints
            ->groupBy(
                fn(Endpoint $endpoint) => $endpoint->getGroup() ?? ''
            )
            ->toArray();

        $spec = new OpenApi([]);
        $spec->info = new Info([]);
        $spec->servers = [
            tap(new Server([]), fn(Server $server): mixed => (
            $server->url = $this->config[self::CONFIG_API_URL]
            )),
        ];
        $spec->openapi = self::OPEN_API_SPEC_VERSION;
        $spec->paths = new Paths([]);

        $this->applyInfo($spec, $this->config);

        foreach ($grouped as $group => $groupedEndpoints) {
            $tag = new Tag([]);
            $group = $group ?: $this->config[self::CONFIG_DEFAULT_GROUP];

            $tag->name = $group;
            $spec->tags = [
                ...$spec->tags,
                $tag,
            ];

            /** @var Endpoint $endpoint */
            foreach ($groupedEndpoints as $endpoint) {
                // URIs must start with a slash in the OpenAPI spec, so ensure
                // we have only a single one at the start
                $uri = '/' . ltrim($endpoint->getUri(), '/');

                $pathItem = $spec->paths->hasPath($uri)
                    ? $spec->paths->getPath($uri)
                    : new PathItem([]);

                // Helps static analysis, shouldn't be possible
                assert($pathItem !== null);

                foreach ($endpoint->getRequestMethods() as $method) {
                    $method = strtolower($method);

                    if ( ! in_array(
                        $method,
                        self::SUPPORTED_REQUEST_METHODS
                    )) {
                        Log::error("Skipping unknown method {$method}");

                        continue;
                    }

                    $operation = new Operation([]);

                    $this->applyOperationId(
                        $operation,
                        $endpoint,
                        $method
                    );
                    $this->applyGroup($operation, $group);
                    $this->applyTitle($operation, $endpoint);
                    $this->applyDescription($operation, $endpoint);
                    $this->applyUrlParams($operation, $endpoint);
                    $this->applyQueryParams($operation, $endpoint);
                    $this->applyResponses($operation, $endpoint);

                    if (in_array($method, [
                        'post',
                        'put',
                        'patch',
                    ], true)) {
                        $this->applyRequestBody(
                            $operation,
                            $endpoint
                        );
                    }

                    // Using a property access is sadly the only viable way to
                    // add the operation to the path item
                    $pathItem->{$method} = $operation;
                }

                $spec->paths->addPath(
                    $uri,
                    $pathItem
                );
            }
        }

        if ( ! $spec->validate()) {
            throw new PrinterException(
                'Invalid OpenAPI specification: ' . implode(
                    ', ',
                    array_unique($spec->getErrors())
                )
            );
        }

        $this->dump($spec);
    }

    #[Pure]
    protected function defaultConfig(): array
    {
        return [
            self::CONFIG_TITLE => 'API',
            self::CONFIG_DESCRIPTION => 'API Documentation generated by Herodot',
            self::CONFIG_VERSION => '1.0.0',
            self::CONFIG_API_URL => '',
            self::CONFIG_DEFAULT_GROUP => 'default',
        ];
    }

    /**
     * @param OpenApi $spec
     * @param array   $config
     *
     * @throws TypeErrorException
     */
    protected function applyInfo(OpenApi $spec, array $config): void
    {
        $spec->info->title = $config[self::CONFIG_TITLE];
        $spec->info->description = $config[self::CONFIG_DESCRIPTION];
        $spec->info->version = $config[self::CONFIG_VERSION];

        if ($contact = $config[self::CONFIG_CONTACT] ?? null) {
            $spec->info->contact = new Contact($contact);
        }

        if ($license = $config[self::CONFIG_LICENSE] ?? null) {
            $spec->info->license = new License($license);
        }

        if ($termsOfService = $config[self::CONFIG_TERMS_OF_SERVICE] ?? null) {
            $spec->info->termsOfService = $termsOfService;
        }
    }

    protected function applyTitle(
        Operation $operation,
        Endpoint $endpoint
    ): void {
        $title = $endpoint->getTitle();

        if ( ! $title) {
            return;
        }

        $operation->summary = $title;
    }

    protected function applyDescription(
        Operation $operation,
        Endpoint $endpoint
    ): void {
        $description = $endpoint->getDescription();

        if ( ! $description) {
            return;
        }

        $operation->description = $description;
    }

    protected function applyDeprecation(
        Operation $operation,
        Endpoint $endpoint
    ): void {
        $operation->deprecated = $endpoint->isDeprecated();
    }

    /**
     * The group is commonly implemented as a tag in OpenAPI:
     *
     * @see https://swagger.io/docs/specification/grouping-operations-with-tags/
     *
     * @param Operation $operation
     * @param string    $group
     */
    protected function applyGroup(Operation $operation, string $group): void
    {
        $group = $group ?: $this->config[self::CONFIG_DEFAULT_GROUP];
        $operation->tags = [$group];
    }

    /**
     * Operation IDs uniquely identify the operation, for which SHA1 hashes will
     * probably suffice. Theoretically, we could use the route name here, but
     * that isn't available at all times and may disclose internal information.
     *
     * @param Operation $operation
     * @param Endpoint  $endpoint
     * @param string    $method
     */
    protected function applyOperationId(
        Operation $operation,
        Endpoint $endpoint,
        string $method
    ): void {
        $operation->operationId = sha1(
            $endpoint->getUniqueId() . $method
        );
    }

    /**
     * @param Operation $operation
     * @param Endpoint  $endpoint
     *
     * @throws TypeErrorException
     */
    protected function applyResponses(
        Operation $operation,
        Endpoint $endpoint
    ): void {
        $responses = $endpoint->getResponses();

        if (count($responses) === 0) {
            $responses[] = new Response('', status: 200);
        }

        $operation->responses = array_reduce(
            $responses,
            function (Responses $carry, Response $response) use ($endpoint) {
                $oaResponse = new OAResponse([]);
                $scenario = $response->getScenario();

                $oaResponse->description = $scenario !== Response::DEFAULT_SCENARIO
                    ? $scenario
                    : '';

                $this->applyHeaders(
                    $oaResponse,
                    $endpoint
                );

                $carry->addResponse(
                    (string)$response->getStatus(),
                    $oaResponse
                );

                return $carry;
            },
            new Responses([])
        );
    }

    /**
     * @param OAResponse $response
     * @param Endpoint   $endpoint
     *
     * @throws TypeErrorException
     */
    protected function applyHeaders(
        OAResponse $response,
        Endpoint $endpoint
    ): void {
        $headers = $endpoint->getHeaders();

        if (count($headers) === 0) {
            return;
        }

        $response->headers = array_map(
            fn(Header $header): OAHeader => tap(
                new OAHeader([]),
                static function (OAHeader $oaHeader) use ($header) {
                    $oaHeader->name = $header->getName();
                    $oaHeader->example = $header->getExample();
                }), $headers
        );
    }

    /**
     * @param OAResponse $oaResponse
     * @param Response   $response
     *
     * @throws TypeErrorException
     */
    protected function applyContentType(
        OAResponse $oaResponse,
        Response $response
    ): void {
        $mediaType = new MediaType([]);

        if ($example = $response->getExample()) {
            $mediaType->example = $example;
        }

        $encoding = new Encoding([]);
        $encoding->contentType = $response->getContentType();
        $mediaType->encoding = [$encoding];

        $oaResponse->content = [$mediaType];
    }

    /**
     * @param Operation $operation
     * @param Endpoint  $endpoint
     *
     * @throws TypeErrorException
     */
    protected function applyQueryParams(
        Operation $operation,
        Endpoint $endpoint
    ): void {
        $parameters = array_map(function (QueryParam $param) {
            $parameter = new Parameter([]);
            $parameter->in = 'query';
            $parameter->style = 'form';
            $parameter->explode = true;
            $parameter->name = $param->getName();
            $parameter->example = $param->getExample();
            $parameter->required = $param->isRequired();
            $parameter->deprecated = $param->isDeprecated();
            $parameter->description = $param->getDescription() ?? '';

            $schema = new Schema([]);
            $schema->default = $param->getDefault();
            $schema->readOnly = $param->isReadOnly();
            $schema->writeOnly = $param->isWriteOnly();
            $schema->nullable = $param->getTypeDefinition()->contains(
                NullType::class
            );
            $this->applyType(
                $schema,
                $param->getTypeDefinition()
            );

            $parameter->schema = $schema;

            return $parameter;
        }, $endpoint->getQueryParams());

        $operation->parameters = [
            ...$operation->parameters,
            ...$parameters,
        ];
    }

    /**
     * @param Schema         $schema
     * @param TypeDefinition $typeDefinition
     *
     * @throws TypeErrorException
     */
    protected function applyType(
        Schema $schema,
        TypeDefinition $typeDefinition
    ): void {
        if ($typeDefinition->count() === 1) {
            $type = $this->convertTypeToOpenApi(
                $typeDefinition->first()
            );

            if ($type) {
                $schema->type = $type;
            }

            return;
        }

        $oneOf = [];

        foreach ($typeDefinition->getTypes() as $type) {
            $typeString = $this->convertTypeToOpenApi($type);

            if ( ! $typeString) {
                continue;
            }

            $subSchema = new Schema([]);
            $subSchema->type = $typeString;

            $oneOf[] = $subSchema;
        }

        $schema->oneOf = $oneOf;
    }

    protected function convertTypeToOpenApi(TypeInterface $type): ?string
    {
        return match ($type::class) {
            AnyType::class, StringType::class => 'string',
            IntegerType::class => 'integer',
            FloatType::class => 'number',
            BooleanType::class => 'boolean',
            TemplateType::class => 'array',
            ModelType::class => 'object',
            default => null,
        };
    }

    /**
     * @param Operation $operation
     * @param Endpoint  $endpoint
     *
     * @throws TypeErrorException
     */
    protected function applyUrlParams(
        Operation $operation,
        Endpoint $endpoint
    ): void {
        $parameters = array_map(function (UrlParam $param) {
            $parameter = new Parameter([]);
            $parameter->in = 'path';
            $parameter->style = 'simple';
            $parameter->explode = false;
            $parameter->required = true;

            $parameter->name = $param->getName();
            $parameter->example = $param->getExample();
            $parameter->deprecated = $param->isDeprecated();
            $parameter->description = $param->getDescription() ?? '';

            $schema = new Schema([]);
            $schema->default = $param->getDefault();
            $schema->readOnly = $param->isReadOnly();
            $schema->writeOnly = $param->isWriteOnly();
            $schema->nullable = $param->getTypeDefinition()->contains(
                NullType::class
            );
            $this->applyType(
                $schema,
                $param->getTypeDefinition()
            );

            $parameter->schema = $schema;

            return $parameter;
        }, $endpoint->getUrlParams());

        $operation->parameters = [
            ...$operation->parameters,
            ...$parameters,
        ];
    }

    /**
     * @param Operation $operation
     * @param Endpoint  $endpoint
     *
     * @throws TypeErrorException
     */
    protected function applyRequestBody(
        Operation $operation,
        Endpoint $endpoint
    ): void {
        $bodyParams = $endpoint->getBodyParams();
        $contentTypes = $endpoint->getAcceptedContentTypes()
            ?: [new ContentType('application/json')];
        $requestBody = new RequestBody([]);

        if ( ! $bodyParams) {
            return;
        }

        // Check whether we have at least a single required parameter
        $requestBody->required = array_reduce($bodyParams, fn(
            bool $required,
            BodyParam $param
        ): bool => $required || $param->isRequired(), false);

        $content = [];

        foreach ($contentTypes as $contentType) {
            $mediaType = new MediaType([]);
            $mediaType->encoding = tap(new Encoding([]), fn(
                Encoding $encoding
            ) => $encoding->contentType = $contentType->getName());

            $schema = new Schema([]);
            $schema->type = 'object';
            $schema->description = $contentType->getDescription() ?? '';
            $schema->properties = array_map(function (BodyParam $param) {
                $schema = new Schema([]);
                $schema->type = (string)$param->getTypeDefinition();
                $schema->title = $param->getName();
                $schema->example = $param->getExample();
                $schema->deprecated = $param->isDeprecated();
                $schema->description = $param->getDescription() ?? '';
                $schema->default = $param->getDefault();
                $schema->readOnly = $param->isReadOnly();
                $schema->writeOnly = $param->isWriteOnly();
                $schema->nullable = $param->getTypeDefinition()->contains(
                    NullType::class
                );

                return $schema;
            }, $bodyParams);

            $mediaType->schema = $schema;
            $content[] = $mediaType;
        }

        $requestBody->content = $content;
        $operation->requestBody = $requestBody;
    }
}

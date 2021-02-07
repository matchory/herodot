<?php

declare(strict_types=1);

namespace Matchory\Herodot\Entities;

use Illuminate\Support\Facades\Event;
use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint as Contract;
use Matchory\Herodot\Events\BodyParamAdded;
use Matchory\Herodot\Events\DeprecationAdded;
use Matchory\Herodot\Events\HeaderAdded;
use Matchory\Herodot\Events\QueryParamAdded;
use Matchory\Herodot\Events\ResponseAdded;
use Matchory\Herodot\Events\UrlParamAdded;
use Matchory\Herodot\Interfaces\StructureInterface;
use Matchory\Herodot\Types\TypeDefinition;
use Ramsey\Uuid\Uuid;

use function implode;

class Endpoint implements Contract, StructureInterface
{
    protected bool $hidden = false;

    protected ?string $description = null;

    protected ?Deprecation $deprecation = null;

    protected ?string $hideReason = null;

    protected ?string $group = null;

    protected ?string $title = null;

    /**
     * @var Header[]
     */
    protected array $headers = [];

    /**
     * @var QueryParam[]
     */
    protected array $queryParams = [];

    /**
     * @var UrlParam[]
     */
    protected array $urlParams = [];

    /**
     * @var BodyParam[]
     */
    protected array $bodyParams = [];

    /**
     * @var array<string, mixed>
     */
    protected array $meta = [];

    protected bool $requiresAuthentication = true;

    /**
     * @var ContentType[]
     */
    protected array $acceptedContentTypes = [];

    protected string $uri = '';

    /**
     * @var string[]
     */
    protected array $requestMethods = [];

    protected ?string $domain = null;

    protected ?string $routeName = null;

    /**
     * @var Response[]
     */
    protected array $responses = [];

    public static function __set_state(array $array): static
    {
        return new static();
    }

    /**
     * @inheritDoc
     */
    public function addAcceptedContentType(
        string $contentType,
        ?string $description = null,
        ?array $meta = null
    ): static {
        $this->acceptedContentTypes[] = new ContentType(
            $contentType,
            $description,
            $meta
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBodyParam(
        string $name,
        ?TypeDefinition $typeDefinition = null,
        ?string $description = null,
        bool $required = false,
        mixed $default = null,
        mixed $example = null,
        ?Deprecation $deprecation = null,
        bool $readOnly = false,
        bool $writeOnly = false,
        ?array $validationRules = null,
        ?array $meta = null
    ): static {
        $bodyParam = new BodyParam(
            $name,
            $typeDefinition,
            $description,
            $required,
            $default,
            $example,
            $deprecation,
            $readOnly,
            $writeOnly,
            $validationRules,
            $meta
        );

        $this->bodyParams[] = $bodyParam;

        Event::dispatch(new BodyParamAdded(
            $bodyParam,
            $this
        ));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addHeader(
        string $name,
        mixed $example = null,
        ?array $meta = null
    ): static {
        $header = new Header(
            $name,
            $example,
            $meta
        );

        $this->headers[] = $header;

        Event::dispatch(new HeaderAdded($header, $this));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addMeta(string $key, mixed $value = null): static
    {
        $this->meta[$key] = $value ?? true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addQueryParam(
        string $name,
        ?TypeDefinition $typeDefinition = null,
        ?string $description = null,
        bool $required = false,
        mixed $default = null,
        mixed $example = null,
        ?Deprecation $deprecation = null,
        bool $readOnly = false,
        bool $writeOnly = false,
        ?array $validationRules = null,
        ?array $meta = null
    ): static {
        $queryParam = new QueryParam(
            $name,
            $typeDefinition,
            $description,
            $required,
            $default,
            $example,
            $deprecation,
            $readOnly,
            $writeOnly,
            $validationRules,
            $meta
        );

        $this->queryParams[] = $queryParam;

        Event::dispatch(new QueryParamAdded(
            $queryParam,
            $this
        ));

        return $this;
    }

    public function addResourceResponse(
        string $resourceClass,
        ?string $modelClass = null
    ): static {
        return $this;
    }

    /**
     * Adds a response to the endpoint
     *
     * @param mixed       $example
     * @param string|null $contentType
     * @param int|null    $status
     * @param string|null $scenario
     * @param array|null  $meta
     *
     * @return $this
     */
    public function addResponse(
        mixed $example,
        ?string $contentType = null,
        ?int $status = null,
        ?string $scenario = null,
        ?array $meta = null
    ): static {
        $response = new Response(
            $example,
            $contentType,
            $status,
            $scenario,
            $meta
        );

        $this->responses[] = $response;

        Event::dispatch(new ResponseAdded(
            $response,
            $this
        ));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addUrlParam(
        string $name,
        ?TypeDefinition $typeDefinition = null,
        ?string $description = null,
        bool $required = false,
        mixed $default = null,
        mixed $example = null,
        ?Deprecation $deprecation = null,
        bool $readOnly = false,
        bool $writeOnly = false,
        ?array $validationRules = null,
        ?array $meta = null
    ): static {
        $urlParam = new UrlParam(
            $name,
            $typeDefinition,
            $description,
            $required,
            $default,
            $example,
            $deprecation,
            $readOnly,
            $writeOnly,
            $validationRules,
            $meta
        );

        $this->urlParams[] = $urlParam;

        Event::dispatch(new UrlParamAdded(
            $urlParam,
            $this
        ));

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getAcceptedContentTypes(): array
    {
        return $this->acceptedContentTypes;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getBodyParams(): array
    {
        return $this->bodyParams;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getDeprecation(): ?Deprecation
    {
        return $this->deprecation;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @inheritDoc
     */
    public function setDomain(?string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * @inheritDoc
     */
    public function setGroup(?string $group): static
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getHideReason(): ?string
    {
        return $this->hideReason;
    }

    /**
     * @inheritDoc
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getRequestMethods(): array
    {
        return $this->requestMethods;
    }

    /**
     * @inheritDoc
     */
    public function setRequestMethods(array $requestMethods): static
    {
        $this->requestMethods = $requestMethods;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * @inheritDoc
     */
    public function setRouteName(?string $routeName): static
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUniqueId(): string
    {
        $identifier = implode('.', [
            $this->getDomain(),
            implode($this->getRequestMethods()),
            $this->getUri(),
        ]);

        return Uuid::uuid5(Uuid::NAMESPACE_DNS, $identifier)
                   ->toString();
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function setUri(string $uri): static
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getUrlParams(): array
    {
        return $this->urlParams;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function isDeprecated(): bool
    {
        return $this->deprecation !== null;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @inheritDoc
     */
    public function setHidden(
        bool $hidden = true,
        ?string $reason = null
    ): static {
        $this->hidden = $hidden;
        $this->hideReason = $hidden ? $reason : null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function requiresAuthentication(): bool
    {
        return $this->requiresAuthentication;
    }

    /**
     * @inheritDoc
     */
    public function setDeprecated(
        ?string $reason = null,
        ?string $version = null,
        ?array $meta = null
    ): static {
        $deprecation = new Deprecation(
            $reason,
            $version,
            $meta
        );

        $this->deprecation = $deprecation;

        Event::dispatch(new DeprecationAdded(
            $deprecation,
            $this
        ));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRequiresAuthentication(
        bool $requiresAuthentication
    ): static {
        $this->requiresAuthentication = $requiresAuthentication;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Matchory\Herodot\Contracts;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Entities\BodyParam;
use Matchory\Herodot\Entities\ContentType;
use Matchory\Herodot\Entities\Deprecation;
use Matchory\Herodot\Entities\QueryParam;
use Matchory\Herodot\Entities\Response;
use Matchory\Herodot\Entities\UrlParam;
use Matchory\Herodot\Types\TypeDefinition;

interface Endpoint
{
    /**
     * Adds a content type the endpoint accepts for its request body.
     *
     * @param string      $contentType
     * @param string|null $description
     * @param array|null  $meta
     *
     * @return static
     */
    public function addAcceptedContentType(
        string $contentType,
        ?string $description = null,
        ?array $meta = null
    ): static;

    /**
     * Adds a response header returned by the endpoint.
     *
     * @param string     $name
     * @param mixed|null $example
     * @param array|null $meta
     *
     * @return static
     */
    public function addHeader(
        string $name,
        mixed $example = null,
        ?array $meta = null
    ): static;

    /**
     * Retrieves a unique identifier representing the endpoint. The identifier
     * MUST be unique in the scope of the application and MUST BE determinable.
     *
     * @return string
     */
    public function getUniqueId(): string;

    /**
     * Retrieves the HTTP verbs the endpoint responds to.
     *
     * @return string[]
     */
    #[Pure] public function getRequestMethods(): array;

    /**
     * Sets the HTTP verbs the endpoint route responds to. Implementations MUST
     * ensure verbs are passed as strings, but they MUST NOT validate the
     * content of those strings. In other words, there MUST NOT be a restriction
     * on the allowed HTTP verbs.
     *
     * @param string[] $requestMethods
     *
     * @return static
     */
    public function setRequestMethods(array $requestMethods): static;

    /**
     * Retrieves the URI associated with the endpoint route.
     *
     * @return string
     */
    #[Pure] public function getUri(): string;

    /**
     * Sets the URI associated with the endpoint route.
     *
     * @param string $uri
     *
     * @return static
     */
    public function setUri(string $uri): static;

    /**
     * Retrieves the name of the route associated with the endpoint, if any.
     *
     * @return string|null
     */
    #[Pure] public function getRouteName(): ?string;

    /**
     * Sets the name of the route associated with the endpoint.
     *
     * @param ?string $routeName
     *
     * @return $this
     */
    public function setRouteName(?string $routeName): static;

    /**
     * Retrieves the name of the domain the endpoint route is bound to, if any.
     *
     * @return string|null
     */
    #[Pure] public function getDomain(): ?string;

    /**
     * Sets the domain the endpoint route is bound to.
     *
     * @param string|null $domain
     *
     * @return $this
     */
    public function setDomain(?string $domain): static;

    /**
     * Adds a query parameter to the endpoint.
     *
     * @param string              $name
     * @param TypeDefinition|null $typeDefinition
     * @param string|null         $description
     * @param bool                $required
     * @param mixed|null          $default
     * @param mixed|null          $example
     * @param Deprecation|null    $deprecation
     * @param bool                $readOnly
     * @param bool                $writeOnly
     * @param array|null          $validationRules
     * @param array|null          $meta
     *
     * @return static
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
    ): static;

    /**
     * Adds a URL parameter to the endpoint.
     *
     * @param string              $name
     * @param TypeDefinition|null $typeDefinition
     * @param string|null         $description
     * @param bool                $required
     * @param mixed|null          $default
     * @param mixed|null          $example
     * @param Deprecation|null    $deprecation
     * @param bool                $readOnly
     * @param bool                $writeOnly
     * @param array|null          $validationRules
     * @param array|null          $meta
     *
     * @return static
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
    ): static;

    /**
     * Adds a body parameter to the endpoint.
     *
     * @param string              $name
     * @param TypeDefinition|null $typeDefinition
     * @param string|null         $description
     * @param bool                $required
     * @param mixed|null          $default
     * @param mixed|null          $example
     * @param Deprecation|null    $deprecation
     * @param bool                $readOnly
     * @param bool                $writeOnly
     * @param array|null          $validationRules
     * @param array|null          $meta
     *
     * @return static
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
    ): static;

    /**
     * Checks whether the endpoint is deprecated and should no longer be used.
     *
     * @return bool
     */
    #[Pure] public function isDeprecated(): bool;

    /**
     * Marks the endpoint as deprecated.
     *
     * @param string|null $reason
     * @param string|null $version
     * @param array|null  $meta
     *
     * @return static
     */
    public function setDeprecated(
        ?string $reason = null,
        ?string $version = null,
        ?array $meta = null
    ): static;

    /**
     * Retrieves information about the endpoint deprecation, if any.
     *
     * @return Deprecation|null
     */
    #[Pure] public function getDeprecation(): ?Deprecation;

    /**
     * Checks whether the endpoint is hidden. This MUST cause it to be omitted
     * from any output document, unless explicitly configured to be included
     * through other means.
     *
     * @return bool
     */
    #[Pure] public function isHidden(): bool;

    /**
     * Marks the endpoint as hidden.
     *
     * @param bool        $hidden
     * @param string|null $reason
     *
     * @return static
     */
    public function setHidden(
        bool $hidden = true,
        ?string $reason = null
    ): static;

    /**
     * Retrieves the reason for hiding the endpoint. The reason MUST be reported
     * as null if the endpoint is not hidden.
     *
     * @return string|null
     */
    #[Pure] public function getHideReason(): ?string;

    /**
     * Retrieves the endpoint title. The title MUST NOT contain line breaks.
     *
     * @return string|null
     */
    #[Pure] public function getTitle(): ?string;

    /**
     * Sets the endpoint title.
     *
     * @param ?string $title
     *
     * @return static
     */
    public function setTitle(?string $title): static;

    /**
     * Retrieves the endpoint description. In contrary to the title, the
     * description may be in long form and MUST preserve line breaks.
     *
     * @return string|null
     */
    #[Pure] public function getDescription(): ?string;

    /**
     * Sets the endpoint description.
     *
     * @param ?string $description
     *
     * @return static
     */
    public function setDescription(?string $description): static;

    /**
     * Retrieves the name of the group the endpoint belongs to. If the endpoint
     * does not belong to a group, it MUST return `null`.
     *
     * @return string|null
     */
    #[Pure] public function getGroup(): ?string;

    /**
     * Sets the name of the group the endpoint belongs to. The group name MUST
     * be preserved case-sensitively.
     *
     * @param ?string $group
     *
     * @return $this
     */
    public function setGroup(?string $group): static;

    /**
     * Retrieves all headers of the endpoint.
     *
     * @return array
     */
    #[Pure] public function getHeaders(): array;

    /**
     * Retrieves all query parameters of the endpoint.
     *
     * @return QueryParam[]
     */
    #[Pure] public function getQueryParams(): array;

    /**
     * Retrieves all URL parameters of the endpoint.
     *
     * @return UrlParam[]
     */
    #[Pure] public function getUrlParams(): array;

    /**
     * Retrieves all body parameters of the endpoint.
     *
     * @return BodyParam[]
     */
    #[Pure] public function getBodyParams(): array;

    /**
     * Retrieves all request body encoding types the endpoint accepts.
     *
     * @return ContentType[]
     */
    #[Pure] public function getAcceptedContentTypes(): array;

    /**
     * Checks whether the endpoint requires authentication or is publicly
     * available to anonymous requests. If this is unknown, implementations MUST
     * assume `true` (requires authentication).
     *
     * @return bool
     */
    #[Pure] public function requiresAuthentication(): bool;

    /**
     * Marks the endpoint as requiring authentication (if `true`) or being
     * publicly available (if `false`).
     *
     * @param bool $requiresAuthentication
     *
     * @return $this
     */
    public function setRequiresAuthentication(
        bool $requiresAuthentication
    ): static;

    /**
     * Adds a key-value pair to the endpoint, where the value is optional. If no
     * value or null is supplied, implementations MUST set the value to boolean
     * `true` instead.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addMeta(string $key, mixed $value = null): static;

    /**
     * Retrieves any meta data associated with the endpoint.
     *
     * @return array
     */
    public function getMeta(): array;

    /**
     * Adds a response to the endpoint.
     *
     * @param mixed       $example
     * @param string|null $contentType
     * @param int|null    $status
     * @param string|null $scenario
     * @param array|null  $meta
     *
     * @return static
     */
    public function addResponse(
        mixed $example,
        ?string $contentType = null,
        ?int $status = null,
        ?string $scenario = null,
        ?array $meta = null
    ): static;

    /**
     * @param string      $resourceClass
     * @param string|null $modelClass
     *
     * @return static
     */
    public function addResourceResponse(
        string $resourceClass,
        ?string $modelClass = null
    ): static;

    /**
     * Retrieves all responses.
     *
     * @return Response[]
     */
    public function getResponses(): array;
}

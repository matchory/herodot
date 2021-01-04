---
sidebarDepth: 3
---
Documenting your API
====================
To gather metadata about your API, we parse its source code in multiple passes. We call these passes _strategies_. A strategy is usually scoped to a specific
aspect of your code: Documentation comments, attributes or OpenAPI annotations. Herodot ships with several built-in strategies that should cover most use cases.
You can easily add your own strategies however, to make Herodot understand your domain specific code. [See below](#creating-your-own-strategy) to learn more
about that.

PHP Attributes
--------------
Extracts metadata by parsing well-defined [attributes](https://www.php.net/manual/en/language.attributes.overview.php). As attributes are a language construct
with full support for type hints, named properties and so on, they are _perfectly_ suited for documenting your API.

> **Attribute inheritance:**  
> Attributes can also be set on controllers, causing them to be inherited by all of its methods. This makes it easy to share common attributes by adding them to
> a base class, or setting a group on controllers.

All attributes live in the `Matchory\Herodot\Attributes` namespace, but your IDE should autocomplete them anyway.

### Title
Adds a title to the endpoint. This attribute only has a single parameter for the title text. The title is intended to be used for single-line headings, and thus
you should expect line breaks to be removed.
```php
#[Title('Endpoint title')]
```

#### Available arguments
Herodot accepts the following arguments to `Title`:
```php
#[Title(
    title: 'Endpoint title',
)]
```

- **title**  
  Title text. This is required.

### Description
Adds a description to the endpoint. This attribute only has a single parameter for the description text. In contrary to the title, the description can take any
length. All long-form text will be parsed as markdown (with HTML support) later on, just keep in mind that some output formats may not provide formatting
options or do not support all of them.
```php
#[Description("This is\na description text.")]
```

#### Available arguments
Herodot accepts the following arguments to `Description`:
```php
#[Description(
    description: 'An optional description',
)]
```

- **description**  
  Description text. This is required.

### Group
Adds the endpoint to a group. By default, all endpoints are in a group with an empty name, but by adding the `Group` attribute to an endpoint, you can move them
into another group. This allows an easy way to add structure to your documentation, for example by grouping all user endpoints.  
Endpoints may only be added to a single group. If you're looking for a way to _tag_ endpoints instead, take a look at the
[Meta Attribute](#meta).
```php
#[Group('Users')]
```

#### Available arguments
Herodot accepts the following arguments to `Group`:
```php
#[Group(
    name: 'Users',
    meta: [ 'key' => 'value' ]
)]
```

- **name**  
  Name of the group. This is required, and case-sensitive.
- **meta**  
  An array of key-value pairs with optional, non-standard metadata about the group. Metadata will be stored alongside the attribute and may be used differently
  depending on the output printer.

### Hidden
Marks the endpoint as hidden, subsequently omitting it from the documentation output--unless, that is, you use an output target that explicitly includes hidden
routes (which may be useful for internal developer docs, for example).
```php
#[Hidden]
```

#### Available arguments
Herodot accepts the following arguments to `Hidden`:
```php
#[Hidden(
    reason: 'An optional description',
)]
```

- **reason**  
  Optional reason why the endpoint is hidden. It may be shown _in internal documentation only_ depending on the output printer.

### Internal
Marks the endpoint as internal. Internal endpoints should not be relied upon by end users, because they may be changed or removed at any time, but should still
appear in your documentation for one reason or another.
```php
#[Internal]
```

#### Available arguments
Herodot accepts the following arguments to `Internal`:
```php
#[Internal(
    description: 'An optional description',
    meta: [ 'key' => 'value' ]
)]
```

- **description**  
  An optional description on why this endpoint is internal. This will probably still be visible in the public documentation.
- **meta**  
  An array of key-value pairs with optional, non-standard metadata about the internal status. Metadata will be stored alongside the attribute and may be used
  differently depending on the output printer.

### Deprecated
Marks the endpoint as deprecated. Deprecated endpoints should not be used anymore and may be removed in a future version.
```php
#[Deprecated]
```

#### Available arguments
Herodot accepts the following arguments to `Deprecated`:
```php
#[Deprecated(
    reason: 'Use the newer Users endpoint instead',
    version: '2.0.1',
    meta: [ 'key' => 'value' ]
)]
```

- **reason**  
  An optional reason why the endpoint is deprecated.
- **version**  
  An optional version since which this endpoint is deprecated. Herodot does not (yet) include actual support for API versions due to the complexity involved.
  We're all ears for your ideas, though!
- **meta**  
  An array of key-value pairs with optional, non-standard metadata about the deprecation status. Metadata will be stored alongside the attribute and may be used
  differently depending on the output printer.

### Authenticated
Marks the endpoint as requiring authentication. This attribute is the complement to the [Unauthenticated attribute](#unauthenticated), they are mutually
exclusive. You may optionally specify the guard handling the authentication.
```php
#[Authenticated]
```

#### Available arguments
Herodot accepts the following arguments to `Authenticated`:
```php
#[Authenticated(
    guard: 'api'
)]
```

- **guard**  
  Name of the guard protecting the endpoint.

### Unauthenticated
Marks the endpoint as being publicly available. This attribute is the complement to the [Authenticated attribute](#authenticated), they are mutually exclusive.
```php
#[Unauthenticated]
```

#### Available arguments
Herodot does not accept any arguments to `Unauthenticated`.

### Accepts
Adds an accepted media type to an endpoint, that is, a request body format your endpoint understands and is willing to parse. You may supply any media type to
this attribute, even with wildcard expressions.
```php
#[Accepts('image/*')]
```

#### Available arguments
Herodot accepts the following arguments to `Accepts`:
```php
#[Accepts(
    mediaType: 'image/png',
    description: 'Images in portable net graphics (PNG) format',
    meta: [ 'key' => 'value' ]
)]
```
The media type is the only required argument.

- **mediaType**  
  Name of the media type. This may be a valid media type according to [the HTTP spec](https://tools.ietf.org/html/rfc2616#section-3.7), and accepts wildcards in
  both the type and the subtype (`type/subtype`).
- **description**  
  An optional description of the media type. You can use this to provide usage instructions.
- **meta**  
  An array of key-value pairs with optional, non-standard metadata about the media type. Metadata will be stored alongside the attribute and may be used
  differently depending on the output printer.

### Header
Adds a response header to an endpoint. This allows highlighting headers you use for a special purpose, or custom headers you use. Along with the (required) name
of a header, and a description, you may supply an example of the value your API will respond with.
```php
#[Header('X-Foo')]
```

#### Available arguments
Herodot accepts the following arguments to `Header`:
```php
#[Header(
    name: 'Content-Language',
    description: 'Language of the download file',
    example: 'English',
    meta: [ 'key' => 'value' ]
)]
```
The header name is the only required argument.

- **name**  
  Name of the header. As header names are [case insensitive per the HTTP spec](https://tools.ietf.org/html/rfc2616#section-4.2), Herodot does ignore casing as
  well.
- **description**  
  An optional description of what or how you use the header. If it is a non-standard header, you should also describe the header itself.
- **example**  
  An optional example of a value your API might respond with.
- **meta**  
  An array of key-value pairs with optional, non-standard metadata about the header. Metadata will be stored alongside the attribute and may be used differently
  depending on the output printer.

### URL Parameters
Adds a URL parameter to an endpoint. URL Parameters, or dynamic segments in the URI of the endpoint, are usually represented as a placeholder in the URI: E.g.
`/foo/{bar}`, where `bar` is a dynamic segment. Herodot merges all annotated URL parameter definitions with the actual parameters in the URI detected by the
framework, to make sure no URL parameters go unnoticed.
```php
#[UrlParam('id')]
```

Along with [Query](#query-parameters) and [Body](#body-parameters) parameters, URL parameters support a wide array of arguments. As you're probably mostly not
going to use most of them at once, it is usually most convenient to use
[named arguments](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments):
```php
#[UrlParam('id', description: 'ID of the record', deprecated: true)]
```
Using named arguments, you can selectively set the attributes relevant to your parameter.

#### Available arguments
Herodot accepts the following arguments to `UrlParam`:
```php
#[UrlParam(
    name: 'id',
    type: 'integer',
    description: 'ID of the record',
    required: true,
    default: null,
    example: 42,
    deprecated: true,
    validationRules: 'min:1',
    meta: [ 'key' => 'value' ]
)]
```
The parameter name is the only required argument.

- **name**  
  Name of the URL parameter. It must match the parameter as it occurs in the route, and is required.
- **type**  
  Type of the parameter. As URLs are usually only parsed as strings, there's not so much type information to extract. You should, however, use this to give a
  hint to users what data your API expects. If omitted, the type will default to `string`.   
  Types are parsed using [Herodot's type rules](types.md).
- **description**  
  An optional description of the parameter. The description may be as verbose as you like, and supports multi-line strings.
- **required**  
  Whether the parameter is required. Defaults to `true`.
- **default**  
  An optional default value for the parameter. Note that parameters with a default may not be required.
- **example**  
  An optional example of the expected parameter value.
- **deprecated**  
  Whether the parameter is deprecated and should not be used anymore. Defaults to `false`.
- **validationRules (beta)**  
  Hints on the applied constraints and validations enforced on this parameter. The concept for validation rules is not quite fleshed out yet due to the
  complexity involved.
- **meta**  
  An array of key-value pairs with optional, non-standard metadata about the parameter.

### Query Parameters
Adds a query parameter to an endpoint. Query parameters are part of the URL, and therefore generally only available as strings.
```php
#[QueryParam('page')]
```

Along with [URL](#url-parameters) and [Body](#body-parameters) parameters, query parameters support a wide array of arguments. As you're probably mostly not
going to use most of them at once, it is usually most convenient to use
[named arguments](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments):
```php
#[QueryParam('page', description: 'Results page for the current query', default: 1)]
```
Using named arguments, you can selectively set the attributes relevant to your parameter.

#### Available arguments
Herodot accepts the following arguments to `QueryParam`:
```php
#[QueryParam(
    name: 'page',
    type: 'integer',
    description: 'Results page for the current query',
    required: false,
    default: 1,
    example: 42,
    deprecated: false,
    validationRules: 'min:1',
    meta: [ 'key' => 'value' ]
)]
```
The parameter name is the only required argument.

- **name**  
  Name of the query parameter.
- **type**  
  Type of the parameter. As URLs are usually only parsed as strings, there's not so much type information to extract. You should, however, use this to give a
  hint to users what data your API expects. If omitted, the type will default to `string`.  
  Types are parsed using [Herodot's type rules](types.md).
- **description**  
  An optional description of the parameter. The description may be as verbose as you like, and supports multi-line strings.
- **required**  
  Whether the parameter is required. Defaults to `false`.
- **default**  
  An optional default value for the parameter. Note that parameters with a default may not be required.
- **example**  
  An optional example of the expected parameter value.
- **deprecated**  
  Whether the parameter is deprecated and should not be used anymore. Defaults to `false`.
- **validationRules (beta)**  
  Hints on the applied constraints and validations enforced on this parameter. The concept for validation rules is not quite fleshed out yet due to the
  complexity involved.
- **meta**  
  An array of key-value pairs with optional, non-standard metadata about the parameter.

### Body Parameters

### Response

### ResponseFile

### Meta
Adds arbitrary metadata to an endpoint. This provides for a good way to extend Herodot in ways you see fit, without having to write an extension.
```php
#[Meta('Key')]
```

You may optionally pass an arbitrary value (it's annotated as `mixed`):
```php
#[Meta('Key', 'value')]
```

OpenAPI Annotations
-------------------
Extracts metadata by parsing [OpenAPI annotations](https://github.com/zircote/swagger-php).

DocBlock comments
-----------------
Extracts metadata by parsing docblock comments and analyzing a wide set of tags.

Return Type Hints
-----------------
Extracts metadata by parsing return types of route handlers to detect [API resources](https://laravel.com/docs/8.x/eloquent-resources).

Configuration Overrides
-----------------------
Extracts metadata by applying route overrides from the configuration file. The override strategy will typically supersede other strategies, allowing you to add
manual overrides where necessary, for example for package routes you cannot influence.

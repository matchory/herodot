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

### Description
Adds a description to the endpoint. This attribute only has a single parameter for the description text. In contrary to the title, the description can take any
length. All long-form text will be parsed as markdown (with HTML support) later on, just keep in mind that some output formats may not provide formatting
options or do not support all of them.
```php
#[Description("This is\na description text.")]
```

### Group
Adds the endpoint to a group. By default, all endpoints are in a group with an empty name, but by adding the `Group` attribute to an endpoint, you can move them
into another group. This allows an easy way to add structure to your documentation, for example by grouping all user endpoints.  
Endpoints may only be added to a single group. If you're looking for a way to _tag_ endpoints instead, take a look at the
[Meta Attribute](#meta).
```php
#[Group('Example endpoints')]
```

The `Group` attribute allows including arbitrary metadata as a key-value map:
```php
#[Group('Example endpoints', [
    'key' => 'value'
])]
```
Metadata will be stored alongside the attribute and may be used differently depending on the output printer.

### Hidden
Marks the endpoint as hidden, subsequently omitting it from the documentation output--unless, that is, you use an output target that explicitly includes hidden
routes (which may be useful for internal developer docs, for example).
```php
#[Hidden()]
```

You can optionally specify a reason. It may be shown depending on the output printer.
```php
#[Hidden('An optional reason')]
```

### Internal
Marks the endpoint as internal. Internal endpoints should not be relied upon by end users, because they may be changed or removed at any time, but should still
appear in your documentation for one reason or another.
```php
#[Internal()]
```

You can optionally specify a description:
```php
#[Internal('An optional description')]
```

Additionally, the `Internal` attribute allows including arbitrary metadata as a key-value map:
```php
#[Internal(meta: [
    'key' => 'value'
])]
```
Metadata will be stored alongside the attribute and may be used differently depending on the output printer.

### Deprecated
Marks the endpoint as deprecated. Deprecated endpoints should not be used anymore and may be removed in a future version.
```php
#[Deprecated()]
```

You can optionally specify a reason:
```php
#[Deprecated('An optional reason')]
```

You can also optionally specify a version since which the endpoint is deprecated:
```php
#[Deprecated(version: '2.5.2')]
```
Herodot does not (yet) include actual support for API versions due to the complexity involved. We're all ears for your ideas, though!

Additionally, the `Deprecated` attribute allows including arbitrary metadata as a key-value map:
```php
#[Deprecated(meta: [
    'key' => 'value'
])]
```
Metadata will be stored alongside the attribute and may be used differently depending on the output printer.

### Authenticated
### Unauthenticated
### Accepts
### Header
### URL Parameters
### Query Parameters
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

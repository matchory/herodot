Extraction Strategies
=====================
To gather metadata about your API, we parse its source code in multiple passes. We call these passes _strategies_. A strategy is usually scoped to a specific
aspect of your code: Documentation comments, attributes or OpenAPI annotations. Herodot ships with several built-in strategies that should cover most use cases.
You can easily add your own strategies however, to make Herodot understand your domain specific code. [See below](#creating-your-own-strategy) to learn more
about that.

Attribute Strategy
------------------
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

OpenAPI Strategy
----------------
Extracts metadata by parsing [OpenAPI annotations](https://github.com/zircote/swagger-php).

DocBlock Strategy
-----------------
Extracts metadata by parsing docblock comments and analyzing a wide set of tags.

Return Type Hint Strategy
-------------------------
Extracts metadata by parsing return types of route handlers to detect [API resources](https://laravel.com/docs/8.x/eloquent-resources).

Override Strategy
-----------------
Extracts metadata by applying route overrides from the configuration file. The override strategy will typically supersede other strategies, allowing you to add
manual overrides where necessary, for example for package routes you cannot influence.

Creating your own strategy
--------------------------
All strategies must implement the [`ExtractionStrategy` interface](https://github.com/matchory/herodot/blob/main/src/Contracts/ExtractionStrategy.php). It
requires strategies to implement one central `handle` method. It receives the resolved route, and the endpoint instance used to collect information about this
route. The job of strategies is analyzing the route, and applying modifications to the endpoint.  
This might look like the following:

```php
public function handle(ResolvedRouteInterface $route, Endpoint $endpoint): Endpoint {
    if ($route->getHandlerReflector()->getAttributes(Hidden::class)) {
        $endpoint->setHidden();
    }
    
    return $endpoint;
}
```

Here, we use reflection to check whether the route handler method has the `Hidden` attribute set, and if so, mark the endpoint as hidden accordingly. The
strategy will be invoked for every route found in the application (and matching your [inclusion rules](./configuration.md#route-inclusion-patterns)), so with
these 7 lines of code we have just added support for hidden routes to our documentation! ...which is already built-in, of course 😉

Strategies don't have to be exhaustive: All of them work on the same endpoint instance, and work together to add pieces of information, one by one.

### Defining the priority
All strategies are required to implement the `getPriority` method, which must return an integer priority. Strategies will be sorted by priority, the highest
last, the lowest first. This is the mechanism making sure the [Override strategy](#override-strategy) is run last, unless you choose to overrule it (which we
don't recommend).

For reference, this is the priority map of the built-in strategies:

| Strategy                                                | Priority |
|:--------------------------------------------------------|---------:|
| [Override Strategy](#docblock-strategy)                 |      999 |
| [Attribute Strategy](#attribute-strategy)               |      120 |
| [OpenAPI Strategy](#openapi-strategy)                   |       90 |
| [Return Type Hint Strategy](#return-type-hint-strategy) |       60 |
| [DocBlock Strategy](#docblock-strategy)                 |       30 |

You can fit your strategy in wherever you see fit, but make sure no other strategy overrides your modifications to the endpoints!

### Depending on other strategies
Sometimes, it's essential your strategy is run after another one. For these cases, you can declare dependencies: Herodot will make sure your strategy is invoked
after all dependencies have been executed. This is achieved using a dependency graph in the background, which sorts out any chain of complex dependencies, but
take care not to form cycles: Herodot will bail out in that case.
```php

public function getDependencies(): ?array
{
    return [
        AttributeStrategy::class,
        MyCustomStrategy::class
    ];
}
```

If your strategy does not have any dependencies (which is a good thing), you may simply return `null` or an empty array here.

> Note that dependencies _will_ interfere with priorities. This is by design and allows shifting priorities as required by your documentation, not by the code.
> Most of the time, this won't be a problem though.

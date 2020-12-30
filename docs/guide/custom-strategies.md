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

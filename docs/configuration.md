Configuration Reference
=======================
This section provides information about all available configuration values.

Route Inclusion Patterns
------------------------
Herodot will add all routes to the documentation that match your inclusion patterns. Patterns will be matched against route names and URIs. An asterisk (`*`)
may be used as a wildcard to match any characters.

```php
return [
    'include' => [
        '*', // Include every route
        
        'api.*', // Include all API routes (as identified by their name)
        
        'api/v0/*', // Include all routes with a shared prefix
        
        'auth.login', // Include a single, specific route
    ],
];
```

Route Exclusion Patterns
------------------------
Exclusion patterns allow to selectively _exclude_ previously included routes. Exclusion patterns always precede inclusion patterns. This is useful to make sure
some specific routes won't be included.

```php
return [
    'include' => [
        'internal.*', // Exclude all internal routes        

        '*beta*', // Exclude all beta routes
        
        '/some/specific/route' // Exclude a specific route
    ],
];
```

Active Extraction Strategies
----------------------------
This list contains all active [extraction strategies](./strategies.md) you want to run during information gathering. By default, all available strategies will
be executed, but you can add your own strategy or a third-party extension strategy here.  
Make sure to list all strategies your application requires.

```php
return [
    'strategies' => [
        DocBlockStrategy::class,
        OpenApiStrategy::class,
        AttributeStrategy::class,
        TypeHintStrategy::class,
        OverrideStrategy::class,
    ],
];
```

Code Inference
--------------
Obviously, extracting information from source code without further hints is a little error-prone and might lead to unexpected results due to your code style. To
prevent surprises, all code inference features can be toggled on and off separately in the configuration.

### Hiding methods by underscore prefixes
This setting controls whether route handler methods prefixed with an underscore will be hidden from the documentation output:
```
code_inference.hide_by_underscore
```

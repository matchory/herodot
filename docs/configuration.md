---
sidebar: 'auto'
---
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

OpenAPI
-------
The OpenAPI section allows you to customize the way the [OpenAPI file](guide/generating-docs.md#openapi-specification) is written.
```php
return [
    'openapi' => [
        'version' => '1.0.0',
        'name' => env('APP_NAME'),
        'api_url' => env('APP_URL'),
        'output_format' => 'yaml',
        'output_file' => public_path('openapi.yaml'),
    ],
];
```

### Available Settings
- `version`  
  The current version of your API. Corresponds to the [`info.version`](https://swagger.io/docs/specification/basic-structure/#metadata) field in the spec.
- `name`  
  The name of your API. Corresponds to the [`info.title`](https://swagger.io/docs/specification/basic-structure/#metadata) field in the spec.
- `api_url`  
  The name of your API. Corresponds to the [`servers[0].url`](https://swagger.io/docs/specification/basic-structure/#servers) field in the spec.
- `output_format`  
  OpenAPI allows JSON or YAML as its file format. Accordingly, you may use `yaml` or `json` here to set the format the OpenAPI writer should use.
- `output_file`  
  The path to the OpenAPI file being generated.

Code Inference
--------------
Obviously, extracting information from source code without further hints is a little error-prone and might lead to unexpected results due to your code style. To
prevent surprises, all code inference features can be toggled on and off separately in the configuration.

### Hiding methods by underscore prefixes
This setting controls whether route handler methods prefixed with an underscore will be hidden from the documentation output:
```
code_inference.hide_by_underscore
```

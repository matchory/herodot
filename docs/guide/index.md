Introduction
============
Herodot supports several different ways to add metadata to your endpoints. We call them _strategies_.  
This package ships with several different strategies (but you can easily [add your own](custom-strategies.md)): PHP Attributes,
documentation comments, OpenAPI annotations (thanks to [zircote/swagger-php](https://github.com/zircote/swagger-php)), static analysis and overrides from your
configuration file.

The preferred way to document your API with Herodot is using [PHP attributes](https://stitcher.io/blog/attributes-in-php-8). This allows a fluent, simple, and
discoverable way to add endpoint metadata by adding attributes:
```php
#[Title('Retrieves a user')]
#[UrlParam(name: 'user', description: 'UUID of the user')]
public function single(User $user) {
    // ...
}
```

Check out the [full list of attributes](strategies.md#php-attributes).

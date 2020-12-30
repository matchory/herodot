Documenting your API
====================
Herodot supports several different ways to add metadata to your endpoints. We call them _strategies_.  
This package ships with several different strategies (but you can easily [add your own](./strategies.md#creating-your-own-strategy)): PHP Attributes, 
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

Check out the [full list of attributes](./strategies.md#attribute-strategy).

#### Hiding endpoints from your documentation
Usually, you can hide endpoints by excluding their route. To go the other way around, and exclude a route from its handler, you can mark it as hidden. This will
cause the endpoint to be omitted from the documentation output--unless, that is, you use an output target that explicitly includes hidden routes (which may be
useful for internal developer docs, for example).

- **Attributes**  
  Add the `Matchory\Herodot\Attributes\Hidden` attribute to the route handler:
  ```php
  #[Hidden]
  ```

  To specify an optional reason, pass it to the attribute:
  ```php
  #[Hidden('An optional reason')]
  ```
- **Documentation Tags**  
  Add the `@hidden` tag to the documentation comment:
  ```php
  /**
   * @hidden
   */
  ```
  To specify an optional reason, add it to the tag:
  ```php
  /**
   * @hidden An optional reason
   */
  ```
- **Overrides**  
  Add the `hidden` override to the endpoint:
  ```php
  'route.name' => [
    'hidden'=> true
  ]
  ```
  To specify an optional reason, set it to a string value:
  ```php
  'route.name' => [
    'hidden'=> 'An optional reason'
  ]
  ```
- Code Inference:
  _This must be enabled using the [#](configuration.md#hiding-methods-by-underscore-prefixes) flag._ Prefix the method with an underscore:
  ```php
  public function _hiddenMethod()
  ```
  Using code inference only, it isn't possible to add a reason.

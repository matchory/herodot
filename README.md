Herodot
=======
> A versatile documentation generator for APIs built with Laravel.

Herodot is a documentation generation framework, tailored for Laravel applications. It works by analyzing the source code of your application, and generating
documentation from it.  
While everything usually works fine out of the box, Herodot is completely modular, configurable, and provides hooks and events in lots of interesting places.

**Features:**
- _Carefully built for Laravel:_  
  Herodot takes every piece of a Laravel app under consideration: From clever route parsing, API resource, policy, and built-in middleware support to
  integration with Passport, Fortify, Sanctum, Scout and others. The more you stick to standards, the better it gets.
- _Adapts to your way of documenting:_  
  Herodot provides strategies for PHP8+ attributes, OpenAPI annotations, documentation comments, external data sources, or just source code parsing. 
  No matter _how_ you prefer to document your API, Herodot will understand it.
- _Separate route collection, information extraction and output generation:_  
  Herodot uses fully isolated phases, making it possible to extend and swap out implementations like Lego bricks.
- _Extensively documented:_  
  Herodot ships with an extensive documentation that goes from simple setup to writing extensions.

**Requirements:**
- PHP >= 8.0 (see [why we need PHP 8](#why-we-need-php-8))
- Laravel >= 7.0 (older versions/Lumen _might_ work, but are neither tested nor optimized)

**Alternatives:**  
- [Scribe](https://github.com/knuckleswtf/scribe)
- [Laravel API Documentation Generator](https://github.com/mpociot/laravel-apidoc-generator)

Getting started
---------------
To start using Herodot, install it via composer:
```bash
php composer require matchory/herodot
```

Unless you've disabled package auto-discovery, Herodot should be installed and available. Otherwise, add the service provider to your `config/app.php`:
```php
'providers' => [
    // ...
    Matchory\Herodot\HerodotServiceProvider::class,
],
```

Usage
-----
Herodot adds one central artisan command to your application:
```bash
php artisan herodot:generate
```

Executing it will start the documentation generation. You can do this now, safely: Herodot will analyze your code and generate documentation at `public/docs`.
If this directory exists already, you will be prompted before anything is written.  
By default, this should leave you with an HTML page, and an OpenAPI (aka. Swagger) definition. To configure the output formats, and any of the other settings,
you should publish the package configuration file:
```bash
php artisan vendor:publish --provider="Matchory\\Herodot\\HerodotServiceProvider" --tag="config"
```

This causes the configuration file to be published to `config/herodot.php`. Check out the [configuration reference](#configuration-reference) to learn about all
available options.

Documenting your API
--------------------
Herodot supports several different ways to add metadata to your endpoints. We call them _strategies_.  
This package ships with several different strategies (but you can easily [add your own](#adding-an-extraction-strategy)): PHP Attributes, documentation 
comments, OpenAPI annotations (thanks to [zircote/swagger-php](https://github.com/zircote/swagger-php)), static analysis and overrides from your configuration 
file.

The preferred way to document your API with Herodot is using [PHP attributes](https://stitcher.io/blog/attributes-in-php-8). This allows a fluent, simple, and
discoverable way to add endpoint metadata by adding attributes:
```php
#[Title('Retrieves a user')]
#[UrlParam(name: 'user', description: 'UUID of the user')]
public function single(User $user) {
    // ...
}
```
Herodot ships with a heap of attributes you can use, but adding your own is as simple as implementing the [`Attribute` contract](./src/Contracts/Attribute.php).
You can find a [reference of all attributes in the documentation](https://matchory.github.io/herodot).

### PHP Attribute Extraction

**Attribute inheritance:**  
Attributes can also be set on controllers, causing them to be inherited by all of its methods. This makes it easy to share common attributes by adding them to a
base class, or setting a group on controllers.

#### Title (`Matchory\Herodot\Attributes\Title`)
Adds a title to the endpoint. This attribute only has a single parameter for the title text. The title is intended to be used for single-line headings, and thus
you should expect line breaks to be removed.
```php
#[Title('Endpoint title')]
```

#### Description (`Matchory\Herodot\Attributes\Description`)
Adds a description to the endpoint. This attribute only has a single parameter for the description text. In contrary to the title, the description can take any
length. All long-form text will be parsed as markdown (with HTML support) later on, just keep in mind that some output formats may not provide formatting
options or do not support all of them.
```php
#[Description("This is\na description text.")]
```

#### Group (`Matchory\Herodot\Attributes\Group`)
Adds the endpoint to a group. By default, all endpoints are in a group with an empty name, but by adding the `Group` attribute to an endpoint, you can move them
into another group. This allows an easy way to add structure to your documentation, for example by grouping all user endpoints.  
Endpoints may only be added to a single group. If you're looking for a way to _tag_ endpoints instead, take a look at the
[Meta Attribute](#meta-matchoryherodotattributesmeta).
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

#### Hidden (`Matchory\Herodot\Attributes\Hidden`)
Marks the endpoint as hidden, subsequently omitting it from the documentation output--unless, that is, you use an output target that explicitly includes hidden
routes (which may be useful for internal developer docs, for example).
```php
#[Hidden()]
```

You can optionally specify a reason. It may be shown depending on the output printer.
```php
#[Hidden('An optional reason')]
```

#### Internal (`Matchory\Herodot\Attributes\Internal`)
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

#### Deprecated (`Matchory\Herodot\Attributes\Deprecated`)
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

#### Authenticated (`Matchory\Herodot\Attributes\Authenticated`)
#### Unauthenticated (`Matchory\Herodot\Attributes\Unauthenticated`)
#### Accepts (`Matchory\Herodot\Attributes\Accepts`)
#### Header (`Matchory\Herodot\Attributes\Header`)
#### URL Parameters (`Matchory\Herodot\Attributes\UrlParam`)
#### Query Parameters (`Matchory\Herodot\Attributes\QueryParam`)
#### Body Parameters (`Matchory\Herodot\Attributes\BodyParam`)
#### Response (`Matchory\Herodot\Attributes\Response`)
#### ResponseFile (`Matchory\Herodot\Attributes\ResponseFile`)

#### Meta (`Matchory\Herodot\Attributes\Meta`)
Adds arbitrary metadata to an endpoint. This provides for a good way to extend Herodot in ways you see fit, without having to write an extension.
```php
#[Meta('Key')]
```

You may optionally pass an arbitrary value (it's annotated as `mixed`):
```php
#[Meta('Key', 'value')]
```

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
  _This must be enabled using the [#]() flag._ Prefix the method with an underscore:
  ```php
  public function _hiddenMethod()
  ```
  Using code inference only, it isn't possible to add a reason.

### Documentation Comment Extraction

### Type Hint Extraction

### External Override Extraction

Configuration reference
-----------------------
This section provides information about all available configuration values.

### Code inference
Obviously, extracting information from source code without further hints is a little error-prone and might lead to unexpected results due to your code style. To
prevent surprises, all code inference features can be toggled on and off separately in the configuration.

#### Hiding methods by underscore prefixes
This setting controls whether route handler methods prefixed with an underscore will be hidden from the documentation output:
```
code_inference.hide_by_underscore
```

Extension guide
---------------

### Adding an extraction strategy

Why we need PHP 8
-----------------
With PHP 8, lots of super-cool features have been added to the language. What stands out in regard to documentation, though, are 
[attributes](https://www.php.net/manual/en/language.attributes.overview.php): They allow adding structured metadata to declarations in code, without any runtime
overhead. Finally, this makes it possible to swap out docblock annotations with actual code that can be validated, type-hinted and highlighted by IDEs!  
As this is most likely the future of annotating code, we made it a strict requirement.  
If you can't migrate to PHP 8 just yet, we recommend one of the alternatives listed at the top of this document.

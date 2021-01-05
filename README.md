<p align="center">
  <h3 align="center">Herodot</h3>
  <p align="center">
    A versatile documentation generator for APIs built with Laravel.
    <br>
    <a href="https://matchory.github.io/herodot/"><strong>Explore the Documentation »</strong></a>
  </p>
</p>

Herodot is a documentation generation framework, tailored for Laravel applications. It works by analyzing the source code of your application, and generating
documentation from it.  
While everything usually works fine out of the box, Herodot is completely modular, configurable, and provides hooks and events in lots of interesting places.

**Features:**
- _Carefully built for Laravel:_  
  Herodot takes every piece of a Laravel app under consideration: From clever route parsing, API resource, policy, and built-in middleware support to
  integration with Passport, Fortify, Sanctum, Scout and others. The more you stick to standards, the better it gets.
- _Adapts to your way of documenting:_  
  Herodot provides strategies for PHP8+ attributes, OpenAPI annotations, documentation comments, external data sources, or just source code parsing. No matter _
  how_ you prefer to document your API, Herodot will understand it.
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

This causes the configuration file to be published to `config/herodot.php`. Check out the
[configuration reference](https://matchory.github.io/herodot/configuration/) to learn about all available options.

Documenting your API
--------------------
Check out [the documentation](https://matchory.github.io/herodot/guide/strategies.html) to learn how to document your API!

Contributing
------------
Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are *greatly appreciated*.

### Fork the Project
- Create your Feature Branch (`git checkout -b your-name/amazing-feature`)
- Commit your Changes (`git commit -m 'Add some amazing feature'`)
- Push to the Branch (`git push origin your-name/amazing-feature`)
- Open a Pull Request

License
-------
Distributed under the [MIT License](https://spdx.org/licenses/MIT.html). See [LICENSE](./LICENSE.md) for more information.


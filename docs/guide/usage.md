Usage
=====
After [installing Herodot](./installation.md), it adds one central artisan command to your application:
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

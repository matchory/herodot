<?php

declare(strict_types=1);

use Matchory\Herodot\Support\Extraction\AttributeStrategy;
use Matchory\Herodot\Support\Extraction\DocBlockStrategy;
use Matchory\Herodot\Support\Extraction\OpenApiStrategy;
use Matchory\Herodot\Support\Extraction\OverrideStrategy;
use Matchory\Herodot\Support\Extraction\TypeHintStrategy;
use Matchory\Herodot\Support\Printing\JsonPrinter;
use Matchory\Herodot\Support\Printing\OpenApiPrinter;

return [

    /*
    |--------------------------------------------------------------------------
    | Route inclusion patterns
    |--------------------------------------------------------------------------
    |
    | This array of patterns will be used to find routes to include in your
    | documentation. They will be matched against the names and URIs of your
    | routes. If any pattern matches, the route will be included. You can also
    | use an asterisk (*) as a wildcard.
    |
    */
    'include' => [
        '*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route exclusion patterns
    |--------------------------------------------------------------------------
    |
    | This array of patterns will be used to find routes to exclude from your
    | documentation. They will be matched against the names and URIs of your
    | routes. If any pattern matches, the route will be excluded. You can also
    | use an asterisk (*) as a wildcard.
    |
    | Note that exclude rules always precede include rules!
    |
    */
    'exclude' => [],

    /*
    |--------------------------------------------------------------------------
    | Information extraction strategies
    |--------------------------------------------------------------------------
    |
    | This array of class names will be used to extract information from your
    | application code. You can use any of the built-in strategies or supply
    | your own: They will run sequentially to extract the maximum amount of
    | information possible.
    |
    */
    'strategies' => [
        DocBlockStrategy::class,
        OpenApiStrategy::class,
        AttributeStrategy::class,
        TypeHintStrategy::class,
        OverrideStrategy::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Output printers
    |--------------------------------------------------------------------------
    |
    | This array of class names will be used to generate the actual output
    | files with the documentation. Printers can be configured separately.
    |
    | Available printers (built-in):
    | LaravelPrinter, HtmlPrinter, PostmanPrinter, OpenApiPrinter,
    | MarkdownPrinter, PastelPrinter, JsonPrinter, YamlPrinter,
    | PlainTextPrinter.
    |
    | Review the readme for detailed information.
    |
    */
    'printers' => [
        JsonPrinter::class,
        // YamlPrinter::class,
        OpenApiPrinter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Endpoint overrides
    |--------------------------------------------------------------------------
    |
    | This array is a mapping of route names to documentation overrides. It
    | allows you to document vendor routes, or fill in data you just cannot add
    | to your code for one reason or another. It will take precedence over any
    | of the built-in strategies.
    |
    | You can generate the override configuration automatically using artisan
    | by using the `artisan herodot:override <route>` command.
    |
    */
    'overrides' => [],

    /*
    |--------------------------------------------------------------------------
    | OpenAPI Printer Settings
    |--------------------------------------------------------------------------
    |
    | This array contains several settings for the OpenAPI writer included with
    | Herodot by default.
    |
    */
    'open_api' => [
        'version' => '1.0.0',
        'name' => env('APP_NAME'),
        'api_url' => env('APP_URL'),

        // OpenAPI documents can be "yaml" or "json"
        'output_format' => 'yaml',

        // The output file will be written to the
        'output_file' => public_path('openapi.yaml'),
    ],
];

Generating Documentation
========================
Herodot supports several ways to write output docs. We call them _printers_, because they print documentation. You can choose one of the built-in printers, or
[create your own](custom-printers.md).

Blade templates
---------------
Herodot includes routes for the built-in templates out of the box, so unless you [disabled or changed it](../configuration.md#configuration-reference),
navigating to `/docs` should show you the default documentation template.

OpenAPI specification
---------------------
The widely-used [OpenAPI specification](https://swagger.io/docs/specification/about/) allows standard discovery for HTTP-based APIs. Herodot can generate a 
fully standards-compliant OpenAPI files.  
By default, a file named `openapi.yaml` will be placed in your `public/` directory. You can change this in the 
[OpenAPI configuration section](../configuration.md#openapi).

Postman collection
------------------

Markdown
--------

HTML
----

YAML file
---------

JSON file
---------

Extraction Strategies
=====================
To gather metadata about your API, we parse its source code in multiple passes. We call these passes _strategies_. A strategy is usually scoped to a specific
aspect of your code: Documentation comments, attributes or OpenAPI annotations. They aren't limited to these examples, though: You can easily add your own
strategies to make Herodot understand your domain specific code.

Default Strategies
------------------
Herodot ships with a few built-in strategies:

### Attribute Strategy
Extracts metadata by parsing well-defined [attributes](https://www.php.net/manual/en/language.attributes.overview.php).

### OpenAPI Strategy
Extracts metadata by parsing [OpenAPI annotations](https://github.com/zircote/swagger-php).

### DocBlock Strategy
Extracts metadata by parsing docblock comments and analyzing a wide set of tags.

### Return Type Hint Strategy
Extracts metadata by parsing return types of route handlers to detect [API resources](https://laravel.com/docs/8.x/eloquent-resources).

### Override Strategy
Extracts metadata by applying route overrides from the configuration file. The override strategy will typically supersede other strategies, allowing you to add
manual overrides where necessary, for example for package routes you cannot influence.

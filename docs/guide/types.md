Types
=====
Herodot employs a generic, and recursive, type parser. It understands the type of all parameters in your API and can infer metadata from them.

> **TL;DR:** Herodot understands types exactly the way you're used to writing them. Whenever a type is required (such as with parameters or responses), you can
> use primitives, generics, models or unions.

Primitive types
---------------
Herodot has support for the following primitive types, which largely resembles PHP's primitives:

- **boolean**
- **float**
- **integer**
- **null**
- **string**
- **array**

Additionally, there's the special `any` type, which can be used in all places where a type isn't known or cannot be expressed as a [union type](#union-types).

Union types
-----------
Herodot supports union types, that is, types composed out of one or more types. You can declare a union by joining two or more types with a pipe (`|`):
```php
string|integer
```

Template types
--------------
Template types, or generics, are a kind of container for other types: Say, an array of numbers. It could be typed as `array`, but that would omit the additional
information about the array content. Therefore, Herodot allows you to add that information to the type:
```php
array<integer>
```

As that is pretty verbose, you can also write simple array types like the following:
```php
integer[]
```

Template types also support union types:
```php
array<integer|float>
```

Herodot parses those types _recursively_, meaning you may nest the type syntax as much as you like:
```php
array<string|array<float>|integer[]>
```

Model types
-----------
Herodot also supports _model types_, so you may use your model (or API resource) classes as types when annotating API responses, for example.

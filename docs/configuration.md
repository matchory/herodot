Configuration Reference
=======================
This section provides information about all available configuration values.

### Code inference
Obviously, extracting information from source code without further hints is a little error-prone and might lead to unexpected results due to your code style. To
prevent surprises, all code inference features can be toggled on and off separately in the configuration.

#### Hiding methods by underscore prefixes
This setting controls whether route handler methods prefixed with an underscore will be hidden from the documentation output:
```
code_inference.hide_by_underscore
```

---
permalink: /extensions/
---

Extension guide
===============

### Adding an extraction strategy

Why we need PHP 8
-----------------
With PHP 8, lots of super-cool features have been added to the language. What stands out in regard to documentation, though, are
[attributes](https://www.php.net/manual/en/language.attributes.overview.php): They allow adding structured metadata to declarations in code, without any runtime
overhead. Finally, this makes it possible to swap out docblock annotations with actual code that can be validated, type-hinted and highlighted by IDEs!  
As this is most likely the future of annotating code, we made it a strict requirement.  
If you can't migrate to PHP 8 just yet, we recommend one of the alternatives listed at the top of this document.

Installation
============

Prerequisites
-------------
- [PHP >= 8.0](https://www.php.net/releases/8.0/en.php)
- [composer](https://getcomposer.org/)
- [Laravel >= 8.0](https://laravel.com/docs/8.x)

Setup
-----
To use Herodot in your Laravel project, install it using composer:
```bash
composer require matchory/herodot
```

Unless you've disabled package auto-discovery, Herodot should be installed and available. Otherwise, add the service provider to your `config/app.php`:
```php
'providers' => [
    // ...
    Matchory\Herodot\HerodotServiceProvider::class,
],
```

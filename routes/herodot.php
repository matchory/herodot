<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Matchory\Herodot\Http\Controllers\HerodotController;

$middleware = Config::get('documentation.laravel.middleware', []);
$prefix = Config::get('documentation.laravel.docs_url', '/docs');

Route::group([
    'middleware' => $middleware,
    'prefix' => $prefix,
], function () {
    Route::get('', [HerodotController::class, 'webPage'])->name('herodot');
    Route::get('postman.json', [HerodotController::class, 'postman'])->name('herodot.postman');
    Route::get('openapi.yaml', [HerodotController::class, 'openapi'])->name('herodot.openapi');
});

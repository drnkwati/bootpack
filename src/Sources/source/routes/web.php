<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

Route::group([
    'prefix' => Config::get('{{ NAME }}.route.web.prefix'),
    // 'domain' => Config::get('{{ NAME }}.route.domain'),
    // 'domain' => '{account}.myapp.com',
    'middleware' => 'web',
    'as' => '{{ NAME }}::',
    'namespace' => '{{ NAMESPACE }}\Controllers'
], function () {
    // Routes defined here have the web middleware applied
    // like the web.php file in a laravel project
    // They also have an applied controller namespace and a route names.

    Route::middleware('{{ NAME }}')->group(function () {
        // Routes defined here have the self-assigned middleware applied.
        // By default this middleware is empty.
    });
});

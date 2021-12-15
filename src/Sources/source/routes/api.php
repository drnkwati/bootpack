<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

Route::group([
    'prefix' => Config::get('{{ NAME }}.route.api.prefix'),
    // 'domain' => Config::get('{{ NAME }}.route.domain'),
    // 'domain' => '{account}.myapp.com',
    'middleware' => 'api',
    'as' => '{{ NAME }}::',
    'namespace' => '{{ NAMESPACE }}\Controllers'
], function () {
    // Routes defined here have the api middleware applied
    // like the api.php file in a laravel project
    // They also have an applied controller namespace and a route names.
});

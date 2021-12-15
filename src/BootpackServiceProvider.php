<?php
namespace Drnkwati\Bootpack;

use Illuminate\Support\ServiceProvider;

class BootpackServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/Config/bootpack.php' => config_path('bootpack.php')], 'bootpack_config');

        if ($this->app->runningInConsole()) {
            $this->commands([Commands\BootpackCreatePackage::class]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/bootpack.php', 'bootpack');
    }
}

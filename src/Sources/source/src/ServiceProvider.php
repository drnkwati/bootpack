<?php
namespace {{ NAMESPACE }};

use Illuminate\Support\ServiceProvider;

class {{ UCNAME }}ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->aliasMiddleware();

        $this->publishes([
            __DIR__.'/../config/{{ NAME }}.php' => config_path('{{ NAME }}.php'),
        ], '{{ NAME }}_config');

        //
        $this->loadFrom();

        // >=5.1
        $this->loadTranslationsFrom($path = __DIR__ . '/../resources/lang', '{{ NAME }}');

        if (method_exists($translator = $this->app['translator'], 'addJsonPath')) {
            $translator->addJsonPath($path);
        }

        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->langPath() . '/vendor/{{ NAME }}'
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', '{{ NAME }}');

        $this->publishes([
            __DIR__ . '/../resources/views' => dirname($this->app->langPath()) . '/views/vendor/{{ NAME }}'
        ]);

        $this->publishes([
            __DIR__ . '/../public' => $this->app->publicPath('vendor/{{ NAME }}')
        ], '{{ NAME }}_assets');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \{{ NAMESPACE }}\Commands\{{ UCNAME }}Command::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(\Illuminate\Database\Eloquent\Factory::class)->load(__DIR__ . '/../database/factories');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/{{ NAME }}.php', '{{ NAME }}'
        );
    }

    /**
     * @return void
     */
    public function loadFrom()
    {
        $apiRoutes = __DIR__ . '/../routes/api.php';
        $webRoutes = __DIR__ . '/../routes/web.php';
        $migration = __DIR__ . '/../database/migrations';

        if (method_exists($this, 'loadRoutesFrom')) {
            // >=5.3
            $this->loadRoutesFrom($apiRoutes);

            $this->loadRoutesFrom($webRoutes);

            $this->loadMigrationsFrom($migration);
        } else {
            $this->loadRoutes($apiRoutes);

            $this->loadRoutes($webRoutes);

            $this->loadMigrations($migration);
        }
    }

    /**
     * @return void
     */
    protected function aliasMiddleware()
    {
        // >=5.4
        !method_exists($this->app['router'], __FUNCTION__) ?: $this->app['router']->aliasMiddleware(
            '{{ NAME }}', \{{ NAMESPACE }}\Middleware\{{ UCNAME }}Middleware::class
        );
    }

    /**
     * Load the given routes file if routes are not already cached.
     *
     * @param  string  $path
     * @return void
     */
    protected function loadRoutes($path)
    {
        if (!$this->app->routesAreCached()) {
            require $path;
        }
    }

    /**
     * Register a database migration path.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function loadMigrations($paths)
    {
        $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }
}

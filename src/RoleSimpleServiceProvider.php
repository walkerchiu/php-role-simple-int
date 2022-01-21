<?php

namespace WalkerChiu\RoleSimple;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class RoleSimpleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->app['router']->aliasMiddleware('wkRole' , config('wk-core.class.role-simple.verifyRole'));
        $this->app['router']->aliasMiddleware('wkPermission' , config('wk-core.class.role-simple.verifyPermission'));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/role-simple.php' => config_path('wk-role-simple.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_role_simple_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_role_simple_table.php'
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-role-simple');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-role-simple'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-role-simple.command.cleaner')
            ]);
        }

        config('wk-core.class.role-simple.role')::observe(config('wk-core.class.role-simple.roleObserver'));
        config('wk-core.class.role-simple.permission')::observe(config('wk-core.class.role-simple.permissionObserver'));

        $this->bladeDirectives();
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    private function bladeDirectives()
    {
        if (!class_exists('\Blade'))
            return;

        \Blade::directive('role', function ($expression) {
            return "<?php if (
                \\Auth::check()
                && \\Auth::user()->hasRole({$expression})) : ?>";
        });
        \Blade::directive('endrole', function ($expression) {
            return "<?php endif; ?>";
        });

        \Blade::directive('roles', function ($expression) {
            return "<?php if (
                \\Auth::check()
                && \\Auth::user()->hasRoles({$expression})) : ?>";
        });
        \Blade::directive('endroles', function ($expression) {
            return "<?php endif; ?>";
        });

        \Blade::directive('permission', function ($expression) {
            return "<?php if (
                \\Auth::check()
                && \\Auth::user()->can({$expression})) : ?>";
        });
        \Blade::directive('endpermission', function ($expression) {
            return "<?php endif; ?>";
        });
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-role-simple')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/role-simple.php', 'wk-role-simple'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/role-simple.php', 'role-simple'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}

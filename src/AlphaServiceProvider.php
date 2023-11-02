<?php

namespace Kitamula\Kitchen;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use Kitamula\Kitchen\Middleware\BasicAuthMiddleware;

class AlphaServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        // Console\Commands\ConfigDefault::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'kitchen.basicauth' => Middleware\BasicAuthMiddleware::class,
        'kitchen.ip_restriction' => Middleware\IpRestrictionMiddleware::class,
    ];

    protected $middlewareGroups = [
        'kitchen' => [
            'kitchen.basicauth',
            'kitchen.ip_restriction',
        ],
    ];


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
        $this->registerRouteMiddleware();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Blade汎用
         */
        \Illuminate\Support\Facades\Blade::directive('nl2br', function ($text) {
            return "<?php echo(nl2br(e({$text}))); ?>";
        });
        \Illuminate\Support\Facades\Blade::directive('storage', function ($text = null) {
            return "<?php echo(asset('storage/'. $text)); ?>";
        });
        \Illuminate\Support\Facades\Blade::directive('ogurl', function () {
            return "<?php echo(url()->current()); ?>";
        });
        \Illuminate\Support\Facades\Blade::directive('ogtype', function () {
            return "<?php echo(!empty(\Route::current()) && \Route::current()->getName() && \Route::current()->getName() == 'index') ? 'website' : 'article'; ?>";
        });
        \Illuminate\Support\Facades\Blade::directive('url2link', function ($string) {
            return "<?php echo(preg_replace( '/((?:https?):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/', '<a href=\"$1\" target=\"_blank\">$1</a>', $string )) ?>";
        });

        //
        $this->registerPublishing();


        // 汎用Migration用
        $this->blueprintMacros();

        // Middleware
        $this->app['router']->pushMiddlewareToGroup('basicauth', BasicAuthMiddleware::class);

    }

    /**
     * Migration (Blueprint Macros)
     */
    public function blueprintMacros()
    {
        Blueprint::macro('termDate', function () {
            $this->date('from_at')->nullable();
            $this->date('to_at')->nullable();
        });
        Blueprint::macro('termDateTime', function () {
            $this->dateTime('from_at')->nullable();
            $this->dateTime('to_at')->nullable();
        });
    }

    /**
     * File publishing
     */
    public function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'kitamula-kitchen-config');
        }
    }

    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }

}

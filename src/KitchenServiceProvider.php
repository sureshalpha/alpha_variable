<?php

namespace Kitamula\Kitchen;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use Kitamula\Kitchen\Middleware\BasicAuthMiddleware;

class KitchenServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\Commands\ConfigDefault::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
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

    // Laravel本体のconfigファイルを上書きする
    protected function mergeConfigFor($path, $key)
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $this->app['config']->set($key, array_merge(
                $this->app['config']->get($key, []), require $path
            ));
        }
    }

}

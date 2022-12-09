<?php

namespace Kitamula\Kitchen;

use Illuminate\Support\ServiceProvider;

class KitchenServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        info('kitamula\kitchen is work!');
    }
}

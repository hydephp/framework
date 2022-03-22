<?php

namespace Hyde\Framework;

use Hyde\Framework\Actions\CreatesDefaultDirectories;
use Illuminate\Support\ServiceProvider;

class HydeServiceProvider extends ServiceProvider
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
        (new CreatesDefaultDirectories)->__invoke();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'hyde');
    }
}

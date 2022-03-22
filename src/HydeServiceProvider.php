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

            
        $this->publishes([
            __DIR__.'/../resources/views/layouts' => resource_path('views/vendor/hyde/layouts'),
        ], 'hyde-layouts');

            
        $this->publishes([
            __DIR__.'/../resources/views/components' => resource_path('views/vendor/hyde/components'),
        ], 'hyde-components');

        
        $this->publishes([
            __DIR__.'/../resources/views/pages' => resource_path('views/pages'),
        ], 'hyde-pages');
    }
}

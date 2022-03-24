<?php

namespace Hyde\Framework;

use Illuminate\Support\ServiceProvider;

class HomeServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/views/homepages/welcome.blade.php' => resource_path('views/pages/index.blade.php'),
        ], 'homepage-welcome');

        $this->publishes([
            __DIR__.'/../resources/views/homepages/post-feed.blade.php' => resource_path('views/pages/index.blade.php'),
        ], 'homepage-post-feed');

        $this->publishes([
            __DIR__.'/../resources/views/homepages/blank.blade.php' => resource_path('views/pages/index.blade.php'),
        ], 'homepage-blank');
    }
}

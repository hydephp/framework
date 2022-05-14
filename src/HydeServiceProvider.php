<?php

namespace Hyde\Framework;

use Composer\InstalledVersions;
use Hyde\Framework\Actions\CreatesDefaultDirectories;
use Hyde\Framework\Core\HydeManager;
use Hyde\Framework\Core\HydeManagerContract;
use Illuminate\Support\ServiceProvider;

/**
 * Register and bootstrap Hyde application services.
 */
class HydeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * @deprecated
         */
        $this->app->bind(
            'hyde.version',
            function () {
                return InstalledVersions::getPrettyVersion('hyde/hyde') ?: 'unreleased';
            }
        );

        /**
         * @deprecated
         */
        $this->app->bind(
            'framework.version',
            function () {
                return InstalledVersions::getPrettyVersion('hyde/framework') ?: 'unreleased';
            }
        );

        /**
         * Register the HydeManager implementation class.
         * Swap this out if you want to override the services.
         */
        $this->app->singleton(HydeManagerContract::class, function ($app) {
            return new HydeManager();
        });

        $this->commands([
            Commands\HydePublishHomepageCommand::class,
            Commands\HydeUpdateConfigsCommand::class,
            Commands\HydePublishViewsCommand::class,
            Commands\HydeRebuildStaticSiteCommand::class,
            Commands\HydeBuildStaticSiteCommand::class,
            Commands\HydeMakePostCommand::class,
            Commands\HydeMakePageCommand::class,
            Commands\HydeValidateCommand::class,
            Commands\HydeInstallCommand::class,
            Commands\HydeDebugCommand::class,
            Commands\HydeServeCommand::class,
        ]);
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
            __DIR__.'/../config' => config_path(),
        ], 'configs');

        $this->publishes([
            __DIR__.'/../resources/views/layouts' => resource_path('views/vendor/hyde/layouts'),
        ], 'hyde-layouts');

        $this->publishes([
            __DIR__.'/../resources/views/components' => resource_path('views/vendor/hyde/components'),
        ], 'hyde-components');

        $this->publishes([
            __DIR__.'/../_pages/404.blade.php' => resource_path('views/pages/404.blade.php'),
        ], 'hyde-page-404');
    }
}

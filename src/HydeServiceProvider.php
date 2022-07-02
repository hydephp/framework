<?php

namespace Hyde\Framework;

use Hyde\Framework\Concerns\RegistersDefaultDirectories;
use Hyde\Framework\Contracts\AssetServiceContract;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Framework\Services\AssetService;
use Illuminate\Support\ServiceProvider;

/**
 * Register and bootstrap Hyde application services.
 */
class HydeServiceProvider extends ServiceProvider
{
    use RegistersDefaultDirectories;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(AssetServiceContract::class, AssetService::class);

        $this->registerSourceDirectories([
            BladePage::class => '_pages',
            MarkdownPage::class => '_pages',
            MarkdownPost::class => '_posts',
            DocumentationPage::class => '_docs',
        ]);

        $this->registerOutputDirectories([
            BladePage::class => '',
            MarkdownPage::class => '',
            MarkdownPost::class => 'posts',
            DocumentationPage::class => config('docs.output_directory', 'docs'),
        ]);

        $this->discoverBladeViewsIn('_pages');

        $this->storeCompiledSiteIn(Hyde::path(
            trim(config('hyde.output_directory', '_site'), '/\\')
        ));

        $this->commands([
            Commands\HydePublishHomepageCommand::class,
            Commands\HydeUpdateConfigsCommand::class,
            Commands\HydePublishViewsCommand::class,
            Commands\HydeRebuildStaticSiteCommand::class,
            Commands\HydeBuildStaticSiteCommand::class,
            Commands\HydeBuildSitemapCommand::class,
            Commands\HydeBuildRssFeedCommand::class,
            Commands\HydeBuildSearchCommand::class,
            Commands\HydeMakePostCommand::class,
            Commands\HydeMakePageCommand::class,
            Commands\HydeValidateCommand::class,
            Commands\HydeInstallCommand::class,
            Commands\HydeDebugCommand::class,
            Commands\HydeServeCommand::class,

            Commands\HydePackageDiscoverCommand::class,
        ]);

        $this->registerModuleServiceProviders();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
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
            Hyde::vendorPath('resources/views/pages/404.blade.php') => Hyde::path('_pages/404.blade.php'),
        ], 'hyde-page-404');

        $this->publishes([
            Hyde::vendorPath('resources/views/homepages/welcome.blade.php') => Hyde::path('_pages/index.blade.php'),
        ], 'hyde-welcome-page');
    }

    /**
     * If you are loading Blade views from a different directory,
     * you need to add the path to the view.php config. This is
     * here done automatically when registering this provider.
     */
    protected function discoverBladeViewsIn(string $directory): void
    {
        config(['view.paths' => array_merge(
            config('view.paths', []),
            [base_path($directory)]
        )]);
    }

    /**
     * The absolute path to the directory when the compiled site is stored.
     */
    protected function storeCompiledSiteIn(string $directory): void
    {
        StaticPageBuilder::$outputPath = $directory;
    }

    /**
     * Register module service providers.
     *
     * @todo Make modules configurable.
     */
    protected function registerModuleServiceProviders(): void
    {
        $this->app->register(Modules\DataCollections\DataCollectionServiceProvider::class);
    }
}

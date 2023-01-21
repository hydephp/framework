<?php

declare(strict_types=1);

namespace Hyde\Framework;

use Hyde\Console\HydeConsoleServiceProvider;
use Hyde\Facades\Features;
use Hyde\Foundation\HydeKernel;
use Hyde\Framework\Concerns\RegistersFileLocations;
use Hyde\Framework\Features\DataCollections\DataCollectionServiceProvider;
use Hyde\Framework\Features\Session\SessionServiceProvider;
use Hyde\Framework\Services\AssetService;
use Hyde\Framework\Services\YamlConfigurationService;
use Hyde\Framework\Views\Components\LinkComponent;
use Hyde\Hyde;
use Hyde\Markdown\MarkdownConverter;
use Hyde\Pages\BladePage;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\HtmlPage;
use Hyde\Pages\MarkdownPage;
use Hyde\Pages\MarkdownPost;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Register and bootstrap Hyde application services.
 */
class HydeServiceProvider extends ServiceProvider
{
    use RegistersFileLocations;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->initializeConfiguration();

        $this->app->singleton(AssetService::class, AssetService::class);

        $this->app->singleton(MarkdownConverter::class, function (): MarkdownConverter {
            return new MarkdownConverter();
        });

        Hyde::setSourceRoot(config('hyde.source_root', ''));

        $this->registerPageModels();

        $this->registerSourceDirectories([
            HtmlPage::class => '_pages',
            BladePage::class => '_pages',
            MarkdownPage::class => '_pages',
            MarkdownPost::class => '_posts',
            DocumentationPage::class => '_docs',
        ]);

        $this->registerOutputDirectories([
            HtmlPage::class => '',
            BladePage::class => '',
            MarkdownPage::class => '',
            MarkdownPost::class => 'posts',
            DocumentationPage::class => config('docs.output_directory', 'docs'),
        ]);

        $this->storeCompiledSiteIn(config('site.output_directory', '_site'));

        $this->discoverBladeViewsIn(BladePage::sourceDirectory());

        $this->registerModuleServiceProviders();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'hyde');

        $this->publishes([
            __DIR__.'/../../config' => config_path(),
        ], 'configs');

        $this->publishes([
            __DIR__.'/../../resources/views/layouts' => resource_path('views/vendor/hyde/layouts'),
        ], 'hyde-layouts');

        $this->publishes([
            __DIR__.'/../../resources/views/components' => resource_path('views/vendor/hyde/components'),
        ], 'hyde-components');

        $this->publishes([
            Hyde::vendorPath('resources/views/pages/404.blade.php') => Hyde::path('_pages/404.blade.php'),
        ], 'hyde-page-404');

        $this->publishes([
            Hyde::vendorPath('resources/views/homepages/welcome.blade.php') => Hyde::path('_pages/index.blade.php'),
        ], 'hyde-welcome-page');

        Blade::component('link', LinkComponent::class);

        HydeKernel::getInstance()->readyToBoot();
    }

    protected function initializeConfiguration()
    {
        if (YamlConfigurationService::hasFile()) {
            YamlConfigurationService::boot();
        }
    }

    /**
     * Register the page model classes that Hyde should use.
     */
    protected function registerPageModels(): void
    {
        // TODO use the hyde facade once it gets the method annotations

        if (Features::hasHtmlPages()) {
            HydeKernel::getInstance()->registerPageClass(HtmlPage::class);
        }

        if (Features::hasBladePages()) {
            HydeKernel::getInstance()->registerPageClass(BladePage::class);
        }

        if (Features::hasMarkdownPages()) {
            HydeKernel::getInstance()->registerPageClass(MarkdownPage::class);
        }

        if (Features::hasMarkdownPosts()) {
            HydeKernel::getInstance()->registerPageClass(MarkdownPost::class);
        }

        if (Features::hasDocumentationPages()) {
            HydeKernel::getInstance()->registerPageClass(DocumentationPage::class);
        }
    }

    /**
     * Register module service providers.
     */
    protected function registerModuleServiceProviders(): void
    {
        $this->app->register(SessionServiceProvider::class);
        $this->app->register(HydeConsoleServiceProvider::class);
        $this->app->register(DataCollectionServiceProvider::class);
    }
}

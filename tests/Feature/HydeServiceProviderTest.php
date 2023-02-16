<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use function app;
use function array_map;
use function basename;
use function config;
use function get_class;
use function glob;
use Hyde\Console\ConsoleServiceProvider;
use Hyde\Facades\Site;
use Hyde\Framework\HydeServiceProvider;
use Hyde\Framework\Services\AssetService;
use Hyde\Foundation\HydeCoreExtension;
use Hyde\Hyde;
use Hyde\Pages\BladePage;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\HtmlPage;
use Hyde\Pages\MarkdownPage;
use Hyde\Pages\MarkdownPost;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Artisan;
use function method_exists;

/**
 * @covers \Hyde\Framework\HydeServiceProvider
 * @covers \Hyde\Framework\Concerns\RegistersFileLocations
 * @covers \Hyde\Foundation\Providers\ConfigurationServiceProvider
 * @covers \Hyde\Foundation\Providers\ViewServiceProvider
 */
class HydeServiceProviderTest extends TestCase
{
    protected HydeServiceProvider $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new HydeServiceProvider(app());
    }

    public function test_provider_is_constructed()
    {
        $this->assertInstanceOf(HydeServiceProvider::class, $this->provider);
    }

    public function test_provider_has_register_method()
    {
        $this->assertTrue(method_exists($this->provider, 'register'));
    }

    public function test_provider_has_boot_method()
    {
        $this->assertTrue(method_exists($this->provider, 'boot'));
    }

    public function test_provider_registers_asset_service_contract()
    {
        $this->assertTrue($this->app->bound(AssetService::class));
        $this->assertInstanceOf(AssetService::class, $this->app->make(AssetService::class));
    }

    public function test_provider_registers_source_directories()
    {
        BladePage::$sourceDirectory = '';
        MarkdownPage::$sourceDirectory = '';
        MarkdownPost::$sourceDirectory = '';
        DocumentationPage::$sourceDirectory = '';

        $this->assertEquals('', BladePage::sourceDirectory());
        $this->assertEquals('', MarkdownPage::sourceDirectory());
        $this->assertEquals('', MarkdownPost::sourceDirectory());
        $this->assertEquals('', DocumentationPage::sourceDirectory());

        $this->provider->register();

        $this->assertEquals('_pages', BladePage::sourceDirectory());
        $this->assertEquals('_pages', MarkdownPage::sourceDirectory());
        $this->assertEquals('_posts', MarkdownPost::sourceDirectory());
        $this->assertEquals('_docs', DocumentationPage::sourceDirectory());
    }

    public function test_provider_registers_output_directories()
    {
        BladePage::$outputDirectory = 'foo';
        MarkdownPage::$outputDirectory = 'foo';
        MarkdownPost::$outputDirectory = 'foo';
        DocumentationPage::$outputDirectory = 'foo';

        $this->assertEquals('foo', BladePage::outputDirectory());
        $this->assertEquals('foo', MarkdownPage::outputDirectory());
        $this->assertEquals('foo', MarkdownPost::outputDirectory());
        $this->assertEquals('foo', DocumentationPage::outputDirectory());

        $this->provider->register();

        $this->assertEquals('', BladePage::outputDirectory());
        $this->assertEquals('', MarkdownPage::outputDirectory());
        $this->assertEquals('posts', MarkdownPost::outputDirectory());
        $this->assertEquals('docs', DocumentationPage::outputDirectory());
    }

    public function test_custom_source_roots_are_applied_to_the_page_models()
    {
        $this->assertSame('_pages', BladePage::sourceDirectory());
        $this->assertSame('_pages', MarkdownPage::sourceDirectory());
        $this->assertSame('_posts', MarkdownPost::sourceDirectory());
        $this->assertSame('_docs', DocumentationPage::sourceDirectory());

        config(['hyde.source_root' => 'foo']);

        $this->provider->register();

        $this->assertSame('foo/_pages', BladePage::sourceDirectory());
        $this->assertSame('foo/_pages', MarkdownPage::sourceDirectory());
        $this->assertSame('foo/_posts', MarkdownPost::sourceDirectory());
        $this->assertSame('foo/_docs', DocumentationPage::sourceDirectory());
    }

    public function test_source_root_set_in_config_is_assigned()
    {
        $this->assertSame('', Hyde::getSourceRoot());
        config(['hyde.source_root' => 'foo']);

        $this->assertSame('', Hyde::getSourceRoot());

        $this->provider->register();
        $this->assertSame('foo', Hyde::getSourceRoot());
    }

    public function test_provider_registers_site_output_directory()
    {
        $this->assertEquals('_site', Site::getOutputDirectory());

        config(['hyde.output_directory' => 'foo']);

        $this->provider->register();

        $this->assertEquals('foo', Site::getOutputDirectory());
    }

    public function test_provider_registers_media_directory()
    {
        $this->assertEquals('_media', Hyde::getMediaDirectory());

        config(['hyde.media_directory' => 'foo']);

        $this->provider->register();

        $this->assertEquals('foo', Hyde::getMediaDirectory());
        $this->assertEquals('foo', Hyde::getMediaOutputDirectory());
    }

    public function test_provider_registers_blade_view_discovery_location_for_configured_blade_view_path()
    {
        config(['view.paths' => []]);
        $this->assertEquals([], config('view.paths'));

        $this->provider->register();

        $this->assertEquals([realpath(Hyde::path('_pages'))], config('view.paths'));
    }

    public function test_blade_view_locations_are_only_registered_once_per_key()
    {
        config(['view.paths' => []]);
        $this->assertEquals([], config('view.paths'));

        $this->provider->register();
        $this->provider->register();

        $this->assertEquals([realpath(Hyde::path('_pages'))], config('view.paths'));
    }

    public function test_provider_registers_console_commands()
    {
        $commands = array_map(function ($command) {
            return get_class($command);
        }, Artisan::all());

        $glob = glob(Hyde::vendorPath('src/Console/Commands/*.php'));

        $this->assertNotEmpty($glob);

        foreach ($glob as $file) {
            $class = 'Hyde\Console\Commands\\'.basename($file, '.php');

            $this->assertContains($class, $commands);
        }
    }

    public function test_provider_registers_additional_module_service_providers()
    {
        $this->provider->register();

        $this->assertArrayHasKey(ConsoleServiceProvider::class, $this->app->getLoadedProviders());
    }

    public function test_provider_registers_all_page_model_source_paths()
    {
        // Find all classes in the Hyde\Pages namespace that are not abstract
        $pages = HydeCoreExtension::getPageClasses();

        // Assert we are testing all page models
        $this->assertEquals([
            HtmlPage::class,
            BladePage::class,
            MarkdownPage::class,
            MarkdownPost::class,
            DocumentationPage::class,
        ], $pages);

        /** @var \Hyde\Pages\Concerns\HydePage|string $page */
        foreach ($pages as $page) {
            $page::$sourceDirectory = 'foo';
        }

        $this->provider->register();

        foreach ($pages as $page) {
            $this->assertNotEquals('foo', $page::$sourceDirectory, "Source directory for $page was not set");
        }
    }

    public function test_provider_registers_all_page_model_output_paths()
    {
        $pages = HydeCoreExtension::getPageClasses();

        /** @var \Hyde\Pages\Concerns\HydePage|string $page */
        foreach ($pages as $page) {
            $page::$outputDirectory = 'foo';
        }

        $this->provider->register();

        foreach ($pages as $page) {
            $this->assertNotEquals('foo', $page::$outputDirectory, "Output directory for $page was not set");
        }
    }

    public function test_provider_registers_source_directories_using_options_in_configuration()
    {
        config(['hyde.source_directories' => [
            HtmlPage::class => 'foo',
            BladePage::class => 'foo',
            MarkdownPage::class => 'foo',
            MarkdownPost::class => 'foo',
            DocumentationPage::class => 'foo',
        ]]);

        $this->provider->register();

        $this->assertEquals('foo', HtmlPage::$sourceDirectory);
        $this->assertEquals('foo', BladePage::$sourceDirectory);
        $this->assertEquals('foo', MarkdownPage::$sourceDirectory);
        $this->assertEquals('foo', MarkdownPost::$sourceDirectory);
        $this->assertEquals('foo', DocumentationPage::$sourceDirectory);
    }

    public function test_source_directories_can_be_set_using_kebab_case_class_names()
    {
        config(['hyde.source_directories' => [
            'html-page' => 'foo',
            'blade-page' => 'foo',
            'markdown-page' => 'foo',
            'markdown-post' => 'foo',
            'documentation-page' => 'foo',
        ]]);

        $this->provider->register();

        $this->assertEquals('foo', HtmlPage::$sourceDirectory);
        $this->assertEquals('foo', BladePage::$sourceDirectory);
        $this->assertEquals('foo', MarkdownPage::$sourceDirectory);
        $this->assertEquals('foo', MarkdownPost::$sourceDirectory);
        $this->assertEquals('foo', DocumentationPage::$sourceDirectory);
    }

    public function test_provider_registers_output_directories_using_options_in_configuration()
    {
        config(['hyde.output_directories' => [
            HtmlPage::class => 'foo',
            BladePage::class => 'foo',
            MarkdownPage::class => 'foo',
            MarkdownPost::class => 'foo',
            DocumentationPage::class => 'foo',
        ]]);

        $this->provider->register();

        $this->assertEquals('foo', HtmlPage::$outputDirectory);
        $this->assertEquals('foo', BladePage::$outputDirectory);
        $this->assertEquals('foo', MarkdownPage::$outputDirectory);
        $this->assertEquals('foo', MarkdownPost::$outputDirectory);
        $this->assertEquals('foo', DocumentationPage::$outputDirectory);
    }

    public function test_output_directories_can_be_set_using_kebab_case_class_names()
    {
        config(['hyde.output_directories' => [
            'html-page' => 'foo',
            'blade-page' => 'foo',
            'markdown-page' => 'foo',
            'markdown-post' => 'foo',
            'documentation-page' => 'foo',
        ]]);

        $this->provider->register();

        $this->assertEquals('foo', HtmlPage::$outputDirectory);
        $this->assertEquals('foo', BladePage::$outputDirectory);
        $this->assertEquals('foo', MarkdownPage::$outputDirectory);
        $this->assertEquals('foo', MarkdownPost::$outputDirectory);
        $this->assertEquals('foo', DocumentationPage::$outputDirectory);
    }
}

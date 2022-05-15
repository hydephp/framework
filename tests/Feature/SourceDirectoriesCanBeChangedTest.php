<?php

namespace Tests\Feature;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\BuildService;
use Hyde\Framework\Services\CollectionService;
use Hyde\Framework\StaticPageBuilder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class SourceDirectoriesCanBeChangedTest.
 */
class SourceDirectoriesCanBeChangedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        is_dir(Hyde::path('testSourceDir')) || mkdir(Hyde::path('testSourceDir'));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(Hyde::path('testSourceDir'));

        parent::tearDown();
    }

    public function test_post_directory_baseline()
    {
        $this->assertEquals('_posts', MarkdownPost::$sourceDirectory);
    }

    public function test_posts_directory_can_be_changed()
    {
        MarkdownPost::$sourceDirectory = '_source/posts';
        $this->assertEquals('_source/posts', MarkdownPost::$sourceDirectory);
    }

    public function test_build_service_recognizes_changed_directory()
    {
        MarkdownPost::$sourceDirectory = '_source/posts';

        $this->assertEquals(
            '_source/posts',
            BuildService::getFilePathForModelClassFiles(MarkdownPost::class)
        );
    }

    public function test_autodiscovery_discovers_posts_in_changed_directory()
    {
        // Using a subdirectory in a directory we know exists, to make cleanup easier.
        mkdir(Hyde::path('_posts/test'));
        touch(Hyde::path('_posts/test/test.md'));

        MarkdownPost::$sourceDirectory = '_posts/test';

        $this->assertEquals(
            ['test'],
            CollectionService::getSourceFileListForModel(MarkdownPost::class)
        );

        unlink(Hyde::path('_posts/test/test.md'));
        rmdir(Hyde::path('_posts/test'));
    }

    public function test_markdown_posts_in_changed_directory_can_be_compiled()
    {
        mkdir(Hyde::path('testSourceDir/blog'));
        touch(Hyde::path('testSourceDir/blog/test.md'));

        MarkdownPost::$sourceDirectory = 'testSourceDir/blog';

        // Uses the same logic as the BuildActionRunner for an accurate test.
        new StaticPageBuilder(
            BuildService::getParserInstanceForModel(
                MarkdownPost::class,
                'test'
            )->get(),
            true
        );

        $this->assertFileExists(Hyde::path('_site/posts/test.html'));
        unlink(Hyde::path('_site/posts/test.html'));
    }

    public function test_markdown_pages_in_changed_directory_can_be_compiled()
    {
        mkdir(Hyde::path('testSourceDir/pages'));
        touch(Hyde::path('testSourceDir/pages/test.md'));

        MarkdownPage::$sourceDirectory = 'testSourceDir/pages';

        // Uses the same logic as the BuildActionRunner for an accurate test.
        new StaticPageBuilder(
            BuildService::getParserInstanceForModel(
                MarkdownPage::class,
                'test'
            )->get(),
            true
        );

        $this->assertFileExists(Hyde::path('_site/test.html'));
        unlink(Hyde::path('_site/test.html'));
    }

    public function test_documentation_pages_in_changed_directory_can_be_compiled()
    {
        mkdir(Hyde::path('testSourceDir/documentation'));
        touch(Hyde::path('testSourceDir/documentation/test.md'));

        DocumentationPage::$sourceDirectory = 'testSourceDir/documentation';

        // Uses the same logic as the BuildActionRunner for an accurate test.
        new StaticPageBuilder(
            BuildService::getParserInstanceForModel(
                DocumentationPage::class,
                'test'
            )->get(),
            true
        );

        $this->assertFileExists(Hyde::path('_site/docs/test.html'));
        unlink(Hyde::path('_site/docs/test.html'));
    }

    public function test_blade_pages_in_changed_directory_can_be_compiled()
    {
        mkdir(Hyde::path('testSourceDir/blade'));
        touch(Hyde::path('testSourceDir/blade/test.blade.php'));

        BladePage::$sourceDirectory = 'testSourceDir/blade';
        Config::set('view.paths', ['testSourceDir/blade']);

        // Uses the same logic as the BuildActionRunner for an accurate test.
        new StaticPageBuilder(
            BuildService::getParserInstanceForModel(
                BladePage::class,
                'test'
            )->get(),
            true
        );

        $this->assertFileExists(Hyde::path('_site/test.html'));
        unlink(Hyde::path('_site/test.html'));
    }
}

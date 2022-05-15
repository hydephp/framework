<?php

namespace Tests\Unit;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\BuildService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Class BuildServiceCanFindModelFromCustomSourceFilePathTest.
 *
 * @covers \Hyde\Framework\BuildService::findModelFromFilePath()
 */
class BuildServiceCanFindModelFromCustomSourceFilePathTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        BladePage::$sourceDirectory = '.source/pages';
        MarkdownPage::$sourceDirectory = '.source/pages';
        MarkdownPost::$sourceDirectory = '.source/posts';
        DocumentationPage::$sourceDirectory = '.source/docs';
        Config::set('view.paths', ['.source/docs']);
    }

    public function test_method_can_find_blade_pages()
    {
        $this->assertEquals(
            BladePage::class,
            BuildService::findModelFromFilePath('.source/pages/test.blade.php')
        );
    }

    public function test_method_can_find_markdown_pages()
    {
        $this->assertEquals(
            MarkdownPage::class,
            BuildService::findModelFromFilePath('.source/pages/test.md')
        );
    }

    public function test_method_can_find_markdown_posts()
    {
        $this->assertEquals(
            MarkdownPost::class,
            BuildService::findModelFromFilePath('.source/posts/test.md')
        );
    }

    public function test_method_can_find_documentation_pages()
    {
        $this->assertEquals(
            DocumentationPage::class,
            BuildService::findModelFromFilePath('.source/docs/test.md')
        );
    }
}

<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Pages;

use Hyde\Facades\Filesystem;
use Hyde\Pages\BladePage;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\MarkdownPage;
use Hyde\Pages\MarkdownPost;
use Hyde\Testing\TestCase;
use Illuminate\Support\Collection;

/**
 * @see \Hyde\Pages\Concerns\HydePage::all()
 */
class PageModelGetHelperTest extends TestCase
{
    public function test_blade_page_get_helper_returns_blade_page_collection()
    {
        $collection = BladePage::all();
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(BladePage::class, $collection);
    }

    public function test_markdown_page_get_helper_returns_markdown_page_collection()
    {
        Filesystem::touch('_pages/test-page.md');

        $collection = MarkdownPage::all();
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(MarkdownPage::class, $collection);

        Filesystem::unlink('_pages/test-page.md');
    }

    public function test_markdown_post_get_helper_returns_markdown_post_collection()
    {
        Filesystem::touch('_posts/test-post.md');

        $collection = MarkdownPost::all();
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(MarkdownPost::class, $collection);

        Filesystem::unlink('_posts/test-post.md');
    }

    public function test_documentation_page_get_helper_returns_documentation_page_collection()
    {
        Filesystem::touch('_docs/test-page.md');

        $collection = DocumentationPage::all();
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(DocumentationPage::class, $collection);

        Filesystem::unlink('_docs/test-page.md');
    }
}

<?php

namespace Hyde\Framework\Testing\Unit;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Testing\TestCase;
use Illuminate\Support\Collection;

/**
 * @see \Hyde\Framework\Concerns\AbstractPage::all()
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
        Hyde::touch(('_pages/test-page.md'));

        $collection = MarkdownPage::all();
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(MarkdownPage::class, $collection);

        unlink(Hyde::path('_pages/test-page.md'));
    }

    public function test_markdown_post_get_helper_returns_markdown_post_collection()
    {
        Hyde::touch(('_posts/test-post.md'));

        $collection = MarkdownPost::all();
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(MarkdownPost::class, $collection);

        unlink(Hyde::path('_posts/test-post.md'));
    }

    public function test_documentation_page_get_helper_returns_documentation_page_collection()
    {
        Hyde::touch(('_docs/test-page.md'));

        $collection = DocumentationPage::all();
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(DocumentationPage::class, $collection);

        unlink(Hyde::path('_docs/test-page.md'));
    }
}

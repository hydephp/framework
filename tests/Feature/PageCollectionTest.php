<?php

namespace Hyde\Framework\Testing\Feature;

use Hyde\Framework\Foundation\PageCollection;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Testing\TestCase;
use Illuminate\Support\Collection;

/**
 * @covers \Hyde\Framework\Foundation\PageCollection
 */
class PageCollectionTest extends TestCase
{
    protected function withoutDefaultPages(): void
    {
        Hyde::unlink('_pages/404.blade.php');
        Hyde::unlink('_pages/index.blade.php');
    }

    public function test_boot_method_creates_new_page_collection_and_discovers_pages_automatically()
    {
        $collection = PageCollection::boot();
        $this->assertInstanceOf(PageCollection::class, $collection);
        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertEquals([
            '_pages/404.blade.php' => new BladePage('404'),
            '_pages/index.blade.php' => new BladePage('index'),
        ], $collection->toArray());
    }

    public function test_blade_pages_are_discovered()
    {
        $this->file('_pages/foo.blade.php');
        $collection = PageCollection::boot();

        $this->assertArrayHasKey('_pages/foo.blade.php', $collection->toArray());
        $this->assertEquals(new BladePage('foo'), $collection->get('_pages/foo.blade.php'));
    }

    public function test_markdown_pages_are_discovered()
    {
        $this->file('_pages/foo.md');
        $collection = PageCollection::boot();

        $this->assertArrayHasKey('_pages/foo.md', $collection->toArray());
        $this->assertEquals(new MarkdownPage('foo'), $collection->get('_pages/foo.md'));
    }

    public function test_markdown_posts_are_discovered()
    {
        $this->file('_posts/foo.md');
        $collection = PageCollection::boot();

        $this->assertArrayHasKey('_posts/foo.md', $collection->toArray());
        $this->assertEquals(new MarkdownPost('foo'), $collection->get('_posts/foo.md'));
    }

    public function test_documentation_pages_are_discovered()
    {
        $this->file('_docs/foo.md');
        $collection = PageCollection::boot();
        $this->assertArrayHasKey('_docs/foo.md', $collection->toArray());
        $this->assertEquals(new DocumentationPage('foo'), $collection->get('_docs/foo.md'));
    }

    public function test_get_page_returns_parsed_page_object_for_given_source_path()
    {
        $this->file('_pages/foo.blade.php');
        $collection = PageCollection::boot();
        $this->assertEquals(new BladePage('foo'), $collection->getPage('_pages/foo.blade.php'));
    }

    public function test_get_pages_returns_collection_of_pages_of_given_class()
    {
        $this->withoutDefaultPages();

        $this->file('_pages/foo.blade.php');
        $this->file('_pages/foo.md');
        $this->file('_posts/foo.md');
        $this->file('_docs/foo.md');
        $collection = PageCollection::boot();
        $this->assertCount(4, $collection);

        $this->assertContainsOnlyInstancesOf(BladePage::class, $collection->getPages(BladePage::class));
        $this->assertContainsOnlyInstancesOf(MarkdownPage::class, $collection->getPages(MarkdownPage::class));
        $this->assertContainsOnlyInstancesOf(MarkdownPost::class, $collection->getPages(MarkdownPost::class));
        $this->assertContainsOnlyInstancesOf(DocumentationPage::class, $collection->getPages(DocumentationPage::class));

        $this->assertEquals(new BladePage('foo'), $collection->getPages(BladePage::class)->first());
        $this->assertEquals(new MarkdownPage('foo'), $collection->getPages(MarkdownPage::class)->first());
        $this->assertEquals(new MarkdownPost('foo'), $collection->getPages(MarkdownPost::class)->first());
        $this->assertEquals(new DocumentationPage('foo'), $collection->getPages(DocumentationPage::class)->first());

        $this->restoreDefaultPages();
    }

    public function test_get_pages_returns_all_pages_when_not_supplied_with_class_string()
    {
        $this->withoutDefaultPages();

        $this->file('_pages/foo.blade.php');
        $this->file('_pages/foo.md');
        $this->file('_posts/foo.md');
        $this->file('_docs/foo.md');
        $collection = PageCollection::boot()->getPages();
        $this->assertCount(4, $collection);

        $this->assertEquals(new BladePage('foo'), $collection->get('_pages/foo.blade.php'));
        $this->assertEquals(new MarkdownPage('foo'), $collection->get('_pages/foo.md'));
        $this->assertEquals(new MarkdownPost('foo'), $collection->get('_posts/foo.md'));
        $this->assertEquals(new DocumentationPage('foo'), $collection->get('_docs/foo.md'));

        $this->restoreDefaultPages();
    }

    public function test_get_pages_returns_empty_collection_when_no_pages_are_discovered()
    {
        $this->withoutDefaultPages();
        $collection = PageCollection::boot();
        $this->assertEmpty($collection->getPages());
        $this->restoreDefaultPages();
    }

    public function test_routes_are_not_discovered_for_disabled_features()
    {
        config(['hyde.features' => []]);

        touch('_pages/blade.blade.php');
        touch('_pages/markdown.md');
        touch('_posts/post.md');
        touch('_docs/doc.md');

        $this->assertEmpty(PageCollection::boot());

        unlink('_pages/blade.blade.php');
        unlink('_pages/markdown.md');
        unlink('_posts/post.md');
        unlink('_docs/doc.md');
    }

    public function test_routes_with_custom_source_directories_are_discovered_properly()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_routes_with_custom_output_paths_are_registered_properly()
    {
        $this->markTestSkipped('TODO');
    }
}

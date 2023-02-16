<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Foundation\Kernel\FileCollection;
use Hyde\Hyde;
use Hyde\Pages\BladePage;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\MarkdownPage;
use Hyde\Pages\MarkdownPost;
use Hyde\Support\Filesystem\MediaFile;
use Hyde\Support\Filesystem\SourceFile;
use Hyde\Testing\TestCase;
use Illuminate\Support\Collection;

/**
 * @covers \Hyde\Foundation\Kernel\FileCollection
 * @covers \Hyde\Foundation\Concerns\BaseFoundationCollection
 */
class FileCollectionTest extends TestCase
{
    public function test_boot_method_creates_new_page_collection_and_discovers_pages_automatically()
    {
        $collection = FileCollection::init(Hyde::getInstance())->boot();
        $this->assertInstanceOf(FileCollection::class, $collection);
        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertEquals([
            '_pages/404.blade.php' => new SourceFile('_pages/404.blade.php', BladePage::class),
            '_pages/index.blade.php' => new SourceFile('_pages/index.blade.php', BladePage::class),
            '_media/app.css' => new MediaFile('_media/app.css'),
        ], $collection->all());
    }

    public function test_get_source_files_returns_all_discovered_source_files_when_no_parameter_is_supplied()
    {
        $collection = FileCollection::init(Hyde::getInstance())->boot();

        $this->assertEquals([
            '_pages/404.blade.php' => new SourceFile('_pages/404.blade.php', BladePage::class),
            '_pages/index.blade.php' => new SourceFile('_pages/index.blade.php', BladePage::class),
        ], $collection->getSourceFiles()->all());
    }

    public function test_get_source_files_does_not_include_non_page_source_files()
    {
        $this->withoutDefaultPages();
        $this->file('_pages/foo.txt');

        $collection = FileCollection::init(Hyde::getInstance())->boot();
        $this->assertEquals([], $collection->getSourceFiles()->all());

        $this->restoreDefaultPages();
    }

    public function test_get_media_files_returns_all_discovered_media_files()
    {
        $collection = FileCollection::init(Hyde::getInstance())->boot();
        $this->assertEquals([
            '_media/app.css' => new MediaFile('_media/app.css'),
        ], $collection->getMediaFiles()->all());
    }

    public function test_get_media_files_does_not_include_non_media_files()
    {
        $this->file('_media/foo.blade.php');
        $collection = FileCollection::init(Hyde::getInstance())->boot();
        $this->assertEquals([
            '_media/app.css' => new MediaFile('_media/app.css'),
        ], $collection->getMediaFiles()->all());
    }

    public function test_blade_pages_are_discovered()
    {
        $this->file('_pages/foo.blade.php');
        $collection = FileCollection::init(Hyde::getInstance())->boot();

        $this->assertArrayHasKey('_pages/foo.blade.php', $collection->toArray());
        $this->assertEquals(new SourceFile('_pages/foo.blade.php', BladePage::class), $collection->get('_pages/foo.blade.php'));
    }

    public function test_markdown_pages_are_discovered()
    {
        $this->file('_pages/foo.md');
        $collection = FileCollection::init(Hyde::getInstance())->boot();

        $this->assertArrayHasKey('_pages/foo.md', $collection->toArray());
        $this->assertEquals(new SourceFile('_pages/foo.md', MarkdownPage::class), $collection->get('_pages/foo.md'));
    }

    public function test_markdown_posts_are_discovered()
    {
        $this->file('_posts/foo.md');
        $collection = FileCollection::init(Hyde::getInstance())->boot();

        $this->assertArrayHasKey('_posts/foo.md', $collection->toArray());
        $this->assertEquals(new SourceFile('_posts/foo.md', MarkdownPost::class), $collection->get('_posts/foo.md'));
    }

    public function test_documentation_pages_are_discovered()
    {
        $this->file('_docs/foo.md');
        $collection = FileCollection::init(Hyde::getInstance())->boot();
        $this->assertArrayHasKey('_docs/foo.md', $collection->toArray());
        $this->assertEquals(new SourceFile('_docs/foo.md', DocumentationPage::class), $collection->get('_docs/foo.md'));
    }
}
